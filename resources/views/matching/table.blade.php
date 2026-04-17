<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include CSS for DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include JS for DataTables -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    {{-- <title>Document</title> --}}
</head>
<body>
    <?php
    // dd($query);
    ?>
    <h1>PCC STATUS</h1>
    <table class="w-fit text-center" id="pccTable">
        <thead>
            <tr>
                <th class="table-center">No</th>
                <th class="table-center">Status</th>
                <th class="text-center">Slip</th>
                <th class="text-center">Part No</th>
                <th class="text-center">Part Name</th>
                <th class="text-center">KD Lot No</th>
            </tr>
            {{-- {{ dd($transactions) }} --}}
            @foreach ( $query as $q )
                <tr>
                    <td class="table-center">{{ ($loop->index)+1 }}</td>
                    <td class="table-center">{{ $q->isMatch?"Matched":"Unmatched" }}</td>
                    <td class="text-center">{{ $q->slip_barcode }}</td>
                    <td class="text-center">{{ $q->part_no }}</td>
                    <td class="text-center">{{ $q->part_name }}</td>
                    <td class="text-center">{{ $q->kd_lot_no }}</td>

                    
                </tr>
            @endforeach
            {{-- {{ dd($transactions) }}; --}}
            <tr></tr>
            <tr></tr>
            <tr></tr>
        </thead>
    </table>
<h1>TRANSACTION HISTORY</h1>
<table class="w-fit text-center" id="transactionTable">
    <thead>
        <tr>
            <th class="table-center">No</th>
            <th class="table-center">Status</th>
            <th class="text-center">Slip</th>
            <th class="text-center">Part No PCC</th>
            <th class="text-center">Part No FG</th>
            <th class="text-center">Seq Label FG</th>
            <th class="text-center">Lot No FG</th>
            <th class="text-center">Matching Date</th>
        </tr>
        {{-- {{ dd($transactions) }} --}}
        @foreach ( $transactions as $transaction )
            <tr>
                <td class="table-center">{{ ($loop->index)+1 }}</td>
                <td class="text-center">{{ $transaction->status }}</td>
                <td class="text-center">{{ $transaction->slip_barcode }}</td>
                <td class="text-center">{{ $transaction->part_no_pcc }}</td>
                <td class="text-center">{{ $transaction->part_no_fg }}</td>
                <td class="text-center">{{ $transaction->seq_fg }}</td>
                <td class="text-center">{{ $transaction->lot_no }}</td>
                <td class="text-center">{{ $transaction->created_at }}</td>
                
            </tr>
        @endforeach

        {{-- {{ dd($transactions) }}; --}}

    </thead>
</table>
        
</body>
</html>