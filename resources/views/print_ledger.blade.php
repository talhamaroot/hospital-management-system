<style>
    .receipt {
        width: 100%;
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
        padding: 5px ;
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
        margin-top: 50px;
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
            width: 100%;
            margin-left: 5px;
            padding-right: 10px;
            font-family: Arial, sans-serif;
            font-size: 10px; /* Adjust font size for readability */
        }

        .receipt h1 {
            text-align: center;
            font-size: 20px; /* Larger heading for emphasis */
        }

        .receipt h2 {
            text-align: center;
            font-size: 18px; /* Slightly smaller sub-heading */
        }

        .receipt h3 {
            text-align: center;
            font-size: 16px;
        }

        .receipt p,
        .receipt .receipt-items table th,
        .receipt .receipt-items table td {
            font-size: 12px; /* Smaller paragraph text */
            margin: 5px 0; /* Adjust margins for compactness */
        }

        .receipt .receipt-items {
            margin-top: 10px;
        }

        .receipt .receipt-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt .receipt-items table th,
        .receipt .receipt-items table td {
            padding: 5px; /* Compact padding for table cells */
        }



        .receipt .receipt-footer {
            margin-top: 50px;
        }

        .receipt .receipt-footer p {
            text-align: center;
            font-size: 10px;
            margin: 3px 0; /* Adjust margin for footer */
        }

        .receipt .receipt-footer p:last-child {
            margin-top: 5px;
            font-weight: bold;
        }

        body .receipt {
            visibility: visible !important;
        }

        html, body {
            visibility: hidden;
        }

        .row-start-2 {
            grid-row-start: 0 !important;
        }

    }

</style>

<div class="receipt">

 


    <div class="receipt-items">
        <table border="1" cellspacing="0" cellpadding="4">
            <thead>
            <tr>
                <th>Account</th>
                <th>Date</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $balance = $previous_balance;
            ?>
             <tr>
                
                <td colspan="4">Previous Balance</td>
               
                <td>{{ $balance }}</td>
            </tr>
            @foreach($query as $row)
                    <?php
                    $balance += $row->debit - $row->credit;
                    ?>

                <tr>
                    <td>
                       <?php
                        if ($row->patient_id) {
                            echo $row->patient->name . " (Patient)";
                        }
                        if ($row->employee_id) {
                            echo $row->employee->name . " (Employee)";
                        }
                        if ($row->doctor_id) {
                            echo $row->doctor->name . " (Doctor)";
                        }
                        if ($row->account) {
                            echo $row->account . " (Hospital)";
                        }
                        if ($row->ot_attendant_id) {
                            echo $row->otAttendant->name . " (OT Attendant)";
                        }
                        if ($row->anesthesiologist_id) {
                            echo $row->anesthesiologist->name . " (Anesthesiologist)";
                        }
                       ?>
                    </td>
                    <td>{{\Carbon\Carbon::create($row->created_at)->format('d/m/Y H:i')}}</td>
                    <td>{{ $row->description }}</td>
                    <td>{{ $row->debit }}</td>
                    <td>{{ $row->credit }}</td>
                    <td>{{ $balance }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>



 

</div>


<script>

    window.onafterprint = function(e){
      
        window.close();
    };
    window.print();

</script>
