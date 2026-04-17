<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCC Matching</title>

    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include CSS for DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="{{ asset('logo.png') }}">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include JS for DataTables -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
        .alert {
            padding: 3px 20px;
            margin-left: auto;
            margin-right: auto;
            width: fit-content;
            display: block;
        }

        .form-label {
            padding: 1px;
            margin: 1px;
            /* background-or: red; */
        }

        .form-input {
            padding: 1px;
            margin: 0px;
            /* background-color: red; */
        }

        .form-input-disabled {
            padding: 1px;
            margin: 0px;
        }

        .alert-danger {
            background-color: #f75252;
            color: #fff;
        }

        .alert-success {
            background-color: #12b02c;
            color: #000;
        }

        .alert-warning {
            background-color: #e8db48;
            color: #000;
        }

        .alert-custom {
            background-color: #f75252;
            color: #fff;
        }

        div.row,
        div.col {
            box-sizing: border-box;
        }

        /* .row { /success
            padding: 1px;
            margin: 1px;
        }

        .col {
            padding: 1px;
            margin: 1px;
        } */
        .clearleft {
            padding-left: 0px;
            margin-left: 0px
        }

        .clearright {
            padding-right: 0px;
            margin-right: 0px
        }

        @media screen and (max-width: 600px) {

            .form-label,
            .form-input,
            .form-input-disabled {
                padding: 1% !important;
                margin: 0 !important;
            }

            .col {
                padding: 0 !important;
                margin: 0 !important;
                margin-left: 0.5% !important;
                /* border-style: dotted;
                border-width: 1px; */
                flex: 1;
                min-width: 0;
            }

            .mb-3,
            .,
            .row {
                padding: 0 !important;
                margin: 0 !important;
                display: flex;
                flex-wrap: wrap;
            }
        }
    </style>

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        td {
    white-space: nowrap;
}

        .topnav {
            overflow: hidden;
            background-color: black;
            width: fit-content;
        }

        .topnav a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 3px 3px;
            text-decoration: none;
            font-size: 14px;
            background-color: black
        }

        .topnav a:hover {
            background-color: #ddd;
            color: #444;
        }

        .topnav a.active {
            background-color: #04AA6D;
            color: white;
        }

        .topnav .icon {
            display: none;
        }

        @media screen and (max-width: 600px) {
            .topnav a:not(:first-child) {
                display: none;
            }

            .topnav a.icon {
                float: right;
                display: block;
            }
        }

        @media screen and (max-width: 600px) {
            .topnav.responsive {
                position: relative;
            }

            .topnav.responsive .icon {
                position: absolute;
                right: 0;
                top: 0;
            }

            .topnav.responsive a {
                float: none;
                display: block;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <?php
    
    // dd($interlock->isLocked);
    ?>
    {{-- <div class="topnav" id="myTopnav"> --}}
    {{-- {{ dd($transactions) }} --}}
    {{-- <div class="topnav absolute top-0 left-0" id="myTopnav">
        <a href="#home" class="active">Home</a>
        <a href="/dn/adm">formDN ADM</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
        </a>
    </div> --}}
    {{-- @include('components/navbar-component') --}}
    @if ($interlock->isLocked)
        <div class="bg-[rgba(0,0,0,0.8)] absolute top-0 left-0 w-full h-lvh flex items-center justify-center z-[9999]">
            <div class="w-full mx-4 flex flex-col items-center justify-center bg-white pb-4 rounded-md">  
                <h1 class="text-xl text-center font-bold bg-red-400 w-full h-12 flex items-center justify-center text-white mb-2">This page is Locked</h1>
                <h2 class="text-sm">Mismatch at <b>{{ $interlock->created_at }}</b></h2>
                <h2 class="text-sm">Part PCC : <b>{{ $interlock->part_no_pcc }}</b></h2>
                <h2 class="text-sm">Part FG : <b>{{ $interlock->part_no_fg }}</b></h2>

                <p class="text-center text-sm my-2 font-bold text-red-400">Hubungi Leader untuk Passkey</p>
                <form class="" action="{{ route('matching.unlock') }}" method="POST" class="mt-2">
                    @csrf
                    <div class="md:mb-4 md:mt-3 flex flex-col items-center gap-2 justify-center">
                        {{-- <label for="barcode" class="form-label">SCAN BARCODE</label> --}}
                        {{-- <input type="text" class="form-control" id="barcode" name="barcode" readonly  required autofocus> --}}
                        @if (session('passkey_error'))
                            <div class="bg-red-400 text-white w-full font-bold text-center rounded-md py-1">Passkey salah!</div>
                        @endif
                        <input type="password" placeholder="Unlock Passkey"
                            class="pl-4 w-full border-1 border-solid border-gray-700 form-control h-7 md:h-10 " id="passkey"
                            name="passkey" required autofocus>
                            <button class="bg-green-700 mx-auto px-4 rounded-sm text-white" type="submit">Submit</button>
                    </div>
                </form>
            
            </div>
        </div>
    @endif
    <x-navbar-component>PCC Matching</x-navbar-component>
    {{-- <h1 class="text-center bg-red-400 text-white font-bold text-4xl inline-block mx-auto">Mas ipan</h1>
    <div class="w-8 h-8 bg-blue-400 rounded-full"></div> --}}
    <div class=" mt-2 md:pt-4 ">
        {{-- <h5 class="text-center sm:text-2xl text-xl font-bold mb-2">KANBAN MATCHING</h5> --}}
        <!-- Success message -->
        @if (session('success'))
            <div class="alert alert-success">{!! session('success') !!}</div>
        @endif
        <!-- Error message -->
        @if ($errors->any())
            <div class="alert alert-danger">{!! $errors->first() !!}</div>
        @endif

        @if (session('message'))
            <div class="alert alert-warning">
                {!! session('message') !!}
            </div>
        @endif

        @if (session('message-match'))
            <div class="alert alert-success">
                {!! session('message-match') !!}
            </div>
        @endif

        @if (session('message-reset'))
            <div class="alert alert-warning">
                {!! session('message-reset') !!}
            </div>
        @endif
        <!-- Barcode Matching Form -->
        <form class="" action="{{ route('matching.store') }}" method="POST">
            @csrf
            <div class="md:mb-4 md:mt-3 flex justify-center">
                {{-- <label for="barcode" class="form-label">SCAN BARCODE</label> --}}
                {{-- <input type="text" class="form-control" id="barcode" name="barcode" readonly  required autofocus> --}}
                <input type="text" placeholder="Scan Barcode"
                    class="pl-4 w-1/2 border-1 border-solid border-gray-700 form-control h-7 md:h-10 " id="barcode"
                    name="barcode" readonly onfocus="this.removeAttribute('readonly');" required autofocus>
            </div>
            <div class="container">
                @if (session('message-no-match'))
                    <div class="alert alert-danger">
                        {{-- <i class="fas fa-circle-xmark text-danger"></i> --}}
                        {!! session('message-no-match') !!}
                    </div>
                @endif
                <div class="flex w-full gap-2 xl:gap-10 md:mb-4">
                    <div class="w-1/2">
                        <div class="">
                            <label for="no_job" class="form-label md:text-xl text-xs font-bold">BARCODE PCC</label>
                            <input type="text" class="form-control  form-input h-6 text-sm md:h-8 md:text-base"
                                id="no_job" name="no_job" value="{{ session('slip_barcode') }}" disabled>
                            {{-- <h2 class="">halo semuanya</h2> --}}
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="">
                            <label for="no_job_fg" class="form-label md:text-xl text-xs font-bold">BARCODE FG</label>
                            <div class="input-group">
                                {{-- @if (session('message-no-match'))
                                    <span class="input-group-text bg-danger">
                                        <i class="fas fa-circle-xmark text-warning"></i>
                                    </span>
                                @endif --}}
                                <input type="text" class="form-control form-input h-6 text-sm md:h-8 md:text-base"
                                    id="no_job_fg" name="no_job_fg" value="{{ session('barcode_fg') }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="row"> --}}
                <div class="flex w-full gap-2 md:gap-16 md:mb-4">
                    <div class="w-1/2">
                        <div class="">
                            <label for="barcode_cust" class="form-label md:text-xl text-xs font-bold">PART NO</label>
                            <input type="text" class="form-control form-input md:h-8 md:text-base h-6 text-sm"
                                id="barcode_cust" name="barcode_cust" value="{{ session('part_no') }}" disabled>
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="">
                            <label for="barcode_fg" class="form-label md:text-xl text-xs font-bold">PART NO FG</label>
                            <input type="text"
                                class="form-control  form-input h-6 text-sm md:h-8 md:text-base-disabled  @if (session('message-no-match')) is-invalid @endif"
                                id="barcode_fg" name="barcode_fg" value="{{ session('part_no_fg') }}" disabled>
                        </div>

                    </div>
                </div>
                {{-- <div class="">
                    </div> --}}
                {{-- <div class="w-full md:mb-4  gap-2 md:gap-16 flex">
                        <div class="w-1/2 ">
                                <label for="no_dn" class="form-label md:text-xl text-xs font-bold">SLIP NO</label>
                                <input type="text" class="form-control w-full form-input h-6 text-sm md:h-8 md:text-base clearleft clearright"
                                    id="no_dn" name="no_dn" value="{{ session('slip_no') }}" disabled>
                        </div>
                        <div class="w-1/2 flex gap-2 md:gap-16 ">
                            <div class="w-1/2">
                                <div class="">
                                    <label for="order_kbn" class= "font-bold md:text-xl text-xs form-label">ORDER</label>
                                    <input type="text" class="form-control  form-input h-6 text-sm md:h-8 md:text-base clearleft clearright"
                                        id="order_kbn" name="order_kbn" value="{{ session('order_kbn') }}" disabled>
                                </div>
                            </div>
                            <div class="w-1/2">
                                <div class="">
                                    <label for="match_kbn" class="form-label md:text-xl text-xs font-bold">MATCH </label>
                                    <input type="text" class="form-control  form-input h-6 text-sm md:h-8 md:text-base clearleft clearright"
                                        id="match_kbn" name="match_kbn" value="{{ session('match_kbn') }}" disabled>
                                </div>
                            </div>
                        </div>

                    </div> --}}

                <div class="flex w-full gap-2 md:gap-16 md:mb-4">
                    <div class="w-1/2">

                        <div class="">
                            <label for="no_seq" class="form-label md:text-xl text-xs font-bold">SEQ NO</label>
                            <input type="text" class="form-control form-input h-6 text-sm md:h-8 md:text-base"
                                id="no_seq" name="no_seq" value="{{ session('pcc_seq') }}" disabled>
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="">
                            <label for="no_seq_fg" class="form-label md:text-xl text-xs font-bold">SEQ NO FG</label>
                            <input type="text" class="form-control form-input h-6 text-sm md:h-8 md:text-base"
                                id="no_seq_fg" name="no_seq_fg" value="{{ session('no_seq_fg') }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="flex w-full gap-2 md:gap-16 md:mb-4">
                    <div class="w-1/2">
                        <div class="">
                            <label for="lot_no_fg" class="form-label md:text-xl text-xs font-bold">LOT NO FG</label>
                            <input type="text" class="form-control form-input h-6 text-sm md:h-8 md:text-base"
                                id="lot_no_fg" name="lot_no_fg" value="{{ session('lot_no_fg') }}" disabled>
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                        <div class="">
                            <div class="">
                                <label for="dn_status" class=" form-label text-xs md:text-xl font-bold text-center md:text-left block mx-auto mb-3">DN
                                    STATUS</label>
                                <div class="">
                                    <span
                                        class="badge {{ session('dn_status') == 'open' ? 'bg-warning w-full text-sm' : 'bg-success w-full' }}"
                                        id="dn_status" name="dn_status">
                                        {{ session('dn_status') }}
                                    </span>
                                </div>
                                <input type="text" class="form-control  form-input clearleft clearright"
                                    id="dn_status" name="dn_status" value="{{ session('dn_status') }}" disabled>
                            </div>
                        </div>
                    </div> --}}



                {{-- </div> --}}

                <div class="mt-5">
                    <button type="submit"
                        class="btn btn-success block mx-auto mt-2 py-1 px-8 text-md md:text-xl btn-sm :btn-md">Submit</button>
                    {{-- <button type="submit" class="block mx-auto btn-success text-white md:font-bold btn-sm btn-md text-md md:text-xl text-md py-1 px-8">Submit</button> --}}
                </div>
        </form>
        <form action="{{ route('matching.reset') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary block mx-auto mt-2 btn-sm :btn-md">Reset Session</button>
        </form>

        <!-- Tombol Reset -->


    </div></br>
    <div class="container  overflow-x-scroll">
        <!-- Transaction Summary Table -->
        {{-- <h5>Transaction Summary</h5> --}}
        <table class="w-fit text-center" width="100%" id="transactionsTable">
            <thead class="w-full">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Slip Barcode</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Part No PCC</th>
                    <th class="text-center">Part No FG</th>
                    <th class="text-center">Lot No FG</th>
                    <th class="text-center">Created At</th>
                    {{-- <th class="text-center">DN Status</th>
                    <th class="text-center">Order Kbn</th>
                    <th class="text-center">Match Kbn</th>
                    <th class="text-center">Cycle</th>
                    <th class="text-center">Job/Part No</th>
                    <th class="text-center">Seq</th>
                    <th class="text-center">Job/Part No FG</th>
                    <th class="text-center">Seq FG</th>
                    <th class="text-center">Created At</th> --}}
                </tr>

            </thead>
        </table>
    </div>

    <script>
        function myFunction() {
            var x = document.getElementById("myTopnav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }
    </script>
    <!-- Add Bootstrap JS (optional, for Bootstrap components like modals or tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script>
        $(document).ready(function() {
            // Set focus on the barcode input field when the page loads
            $('#barcode').focus();

            // Reset focus to barcode input field after form submission or other actions
            $('form').on('submit', function() {
                setTimeout(function() {
                    $('#barcode').focus();
                }, 100); // Delay slightly to ensure focus resets
            });
        });
    </script> --}}
    <script>
        $(document).ready(function() {
            $('#transactionsTable').DataTable({
                processing: true,
                serverSide: true,
                // autoWidth: false,
                responsive: true,
                scrollX: true,
                ajax: '{{ route('transactions.data') }}',
    //             columnDefs: [
    //     { width: "20%", targets: 0 },
    //     { width: "30%", targets: 1 }
    // ],

                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'slip_barcode',
                        width:'100px'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'part_no_pcc',
                        // className: 'nowrap',
                        // width: '300px'
                    },
                    {
                        data: 'part_no_fg',
                        orderable: false
                    },
                    {
                        data: 'lot_no'
                    },
                    {
                        data: 'created_at'
                    },

                ],
                order: [
                    [1, 'asc']
                ], // Mengatur kolom yang harus diurutkan
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50, 100],
                searching: true,

                rawColumns: ['status', 'dn_status'] // Tambahkan ini
            });

            // Tambahkan event listener untuk merender ulang DataTable saat window di-resize
            // $(window).on('resize', function() {
            //     table.columns.adjust().draw();
            // });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barcodeInput = document.getElementById('barcode');

            barcodeInput.addEventListener('focus', function() {
                // Remove readonly when focused to allow input from scanner or keyboard
                barcodeInput.removeAttribute('readonly');
            });

            barcodeInput.addEventListener('blur', function() {
                // Reapply readonly when focus is lost
                barcodeInput.setAttribute('readonly', true);
            });

            // Optionally, you can add a timeout to reapply readonly after input
            barcodeInput.addEventListener('input', function() {
                setTimeout(() => {
                    barcodeInput.setAttribute('readonly', true);
                }, 100); // Adjust the timeout as needed
            });
        });
    </script>
</body>

</html>
