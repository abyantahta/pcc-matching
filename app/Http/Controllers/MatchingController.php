<?php

namespace App\Http\Controllers;

use App\Models\DnADM;
use App\Models\DnADMKEP;
use App\Models\Dashboard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\TransactionsExport;
use App\Exports\TransactionsKEPExport;
use App\Exports\TransactionsKAPExport;
use App\Models\DnADMKAP;
use App\Models\Interlock;
use App\Models\Pcc;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;

class MatchingController extends Controller
{
    public function index()
    {
        // Untuk menampilkan view DataTable
        $transactions = Transaction::all();
        $interlock = Interlock::get()->first();

        // dd($transactions);
        return view('matching.index', compact('transactions','interlock')); // Pastikan ini adalah view yang tepat
    }
    public function exportTransactions(Request $request)
    {
        $dateFilter = $request->query('date_filter') ?: null;
        $statusFilter = $request->query('status_filter') ?: null;
        $fileName = $dateFilter? Carbon::parse($dateFilter)->format('d M Y'):"Full Transactions";
            return Excel::download(new TransactionsExport($dateFilter,$statusFilter), "Log PCC Matching [".$fileName."] .xlsx");
    }
    public function getTransactions(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::query()
                // $transactions = Transaction::select(['barcode_cust', 'no_dn', 'no_job', 'no_seq', 'barcode_fg', 'no_job_fg', 'no_seq_fg', 'status', 'dn_status', 'order_kbn', 'match_kbn','del_cycle', 'created_at'])
                ->latest('created_at'); // Tambahkan ini untuk urutkan dari terbaru;
            // dd($transaction)
            // {{ dd($transaction) }};
            // dd($transactions);
            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('status', function ($transaction) {
                    return '<span class="badge ' . ($transaction->status == 'match' ? 'bg-success' : 'bg-danger') . '">' . ucfirst($transaction->status) . '</span>';
                })
                ->editColumn('created_at', function ($transaction) {
                    return $transaction->created_at->format('d/m/y - H:i'); // Ubah format tanggal di sini
                })

                ->rawColumns(['status', 'created_at']) // Jangan lupa tambahkan 'created_at' di sini
                ->make(true);
            // dd($transaction);
        }
    }
    public function store(Request $request)
    {
        $isLocked = Interlock::get()->first()->isLocked;
        // dd($isLocked);
        if($isLocked){
            return redirect()->back();
        }
        $input = trim($request->input('barcode')); // trim untuk menghilangkan whitespaces
        $input = str_replace(' ','',$input);
        if (str_starts_with($input, '2')) {
            // Check if input is Data1 (starting with "DN" and 26 characters in length DN5124100080185ARC-1066001)
            if (strlen($input) !== 23) {
                return redirect()->back()->withErrors($input . '(L:' . strlen($input) . ') INVALID FORMAT. PASTIKAN SCAN BARCODE SESUAI FORMAT YANG SDH DI REGISTER.');
            }
            
            // Cek apakah barcode_cust sudah ada di database
            $existingDn = Pcc::where('slip_barcode', $input)->latest()->first();
            $existingTransaction = Transaction::where('slip_barcode', $input)->where('status', "match")->latest()->first();
            if ($existingTransaction) {
                return redirect()->back()->withErrors('<span class="badge bg-warning" ><b>DOUBLE</b></span>, ' . $input . '(L:' . strlen($input) . ') Barcode customer  sudah pernah berhasil di matching.');
            }
            if (!$existingDn) {
                return redirect()->back()->withErrors('<span class="badge bg-warning" > <b>NO DATA</b> </span>, ' . $input . ' Belum ada PCC yang di upload di sistem.');
            }
            // $dn_status = "OPEN"
            Session::put('temp_data', [
                'slip_barcode' => $input,
                'slip_no' => $existingDn->slip_no,
                'part_no' => str_replace(' ','',$existingDn->part_no),
                'part_name' => $existingDn->part_name,
                'del_date' => $existingDn->date,
                'pcc_seq' => $existingDn->pcc_count,
                'status' => 'pairing',
                'created_at' => now()
            ]);

            // Store no_dn and no_job in persistent session variables
            Session::put('slip_barcode', $input);
            Session::put('slip_no', $existingDn->slip_no);
            Session::put('part_no', $existingDn->part_no);
            Session::put('part_name', $existingDn->part_name);
            Session::put('del_date', $existingDn->date);
            Session::put('pcc_seq', $existingDn->pcc_count);
            Session::put('status', 'pairing');

            return redirect()->back()->with('message', $input . '(L:' . strlen($input) . ') SILAHKAN SCAN BARCODE FG.');
        }

        $fgBarcodeParts = explode('#', $input);
        if (count($fgBarcodeParts) === 4) {

            $tempData = Session::get('temp_data');
            // dd($input);
            if (!$tempData) {
                return redirect()->back()->withErrors('SCAN BARCODE CUSTOMER SEBELUM BARCODE FG.');
            }
            [$part_no, $_jobNo, $fg_seq, $lot_no] = $fgBarcodeParts;
            $part_no = trim($part_no);
            $fg_seq = trim($fg_seq);
            $lot_no = trim($lot_no);


            Session::flash('barcode_fg', $input);
            Session::flash('part_no_fg', $part_no);
            // Session::flash('part_name_fg', $part_no);
            Session::flash('no_seq_fg', $fg_seq);
            Session::flash('lot_no_fg', $lot_no);
            // dd($tempData);
            // Compare no_job from Data1 and no_job_fg from Data2
            // dd($tempData);
            if ($tempData['part_no'] !== str_replace(' ','',$part_no)) {
                $transaction = new Transaction();
                $transaction->slip_barcode = $tempData['slip_barcode'];
                $transaction->part_no_pcc = $tempData['part_no'];
                $transaction->part_no_fg = $part_no;
                $transaction->seq_fg = $fg_seq;
                $transaction->lot_no = $lot_no;
                $transaction->del_date = $tempData['del_date'];
                $transaction->status = 'mismatch';
                $transaction->created_at = Carbon::now();
                $transaction->save();

                Interlock::query()->first()->update([
                    'isLocked'=>true,
                    'created_at'=> Carbon::now(),
                    'part_no_pcc'=> $tempData['part_no'],
                    'part_no_fg'=> $part_no
                ]);
                try{
                    $response = Http::withHeaders([
                        'Authorization' => 'DcjkiWJ9gwbp7scYKowe',
                    ])->withOptions(['verify' => false])->post('https://api.fonnte.com/send',[
                        'target'=> '089522134460, 081270074197,08111990425',
                        'message' => 'Terjadi mismatch pukul '. Carbon::now()->format('H:i'). '
Segera datang ke line.',
                        'delay' => '2'
                    ]);
                }catch(\Exception $e){

                }

                return redirect()->back()
                    ->with('message-no-match', '<b>' . $part_no . '</b>, TIDAK SESUAI. SCAN ULANG !')
                    ->with('message', 'SILAHKAN SCAN KEMBALI BARCODE FG.');
                    // ->with();
            }
            //cek kalau ada transaksi dengan sequence number label yang sama, kalau sama, ditolak
            $transactionWithDupsSeqNo = Transaction::where('part_no_fg',$part_no)->where('seq_fg',$fg_seq)->where('created_at', '>=', Carbon::now()->subHours(12))->count();
            // dd($transactionWithDupsSeqNo);
            if($transactionWithDupsSeqNo != 0){
                // return back()->with('error','Label Finish Good Part No'.$input. " sudah pernah digunakan.");
                return redirect()->back()->withErrors('<span class="badge bg-warning" ><b>DOUBLE</b></span>, Label Finish Good '.$input. " sudah pernah digunakan");

            }

            $transaction = new Transaction();
            $transaction->slip_barcode = $tempData['slip_barcode'];
            $transaction->part_no_pcc = $tempData['part_no'];
            $transaction->part_no_fg = $part_no;
            $transaction->seq_fg = $fg_seq;
            $transaction->lot_no = $lot_no;
            $transaction->del_date = $tempData['del_date'];
            $transaction->status = 'match';
            $transaction->created_at = now();
            $transaction->save();

            Pcc::where('slip_barcode',$tempData['slip_barcode'])->first()->update(['isMatch'=>true]);

            Session::forget('temp_data');
            // Session::forget('temp_data');

            // Call the resetSession function to clear session data
            $this->resetSession();

            return redirect()->back()->with('message-match', 'MATCH,<br>SLIP BARCODE: <b>' . $tempData['slip_no'] . '</b>, PART: <b>' . $tempData['part_no'] . '</b>,'. '</b><br> TRANSAKSI BERHASIL DI SIMPAN. ');
        }

        // Handle invalid input formats for both data1 and data2
        return redirect()->back()->withErrors($input . '(L:' . strlen($input) . ') INVALID FORMAT. PASTIKAN SCAN BARCODE SESUAI FORMAT YANG SDH DI REGISTER.');
    }

    public function unlock (Request $request){
        $passkey = trim($request->input('passkey')); // trim untuk menghilangkan whitespaces
        // dd($passkey);
        if($passkey !== "SaNkEi2011..!"){
            return redirect()->back()->with('passkey_error','Passkey salah!');
        }
        Interlock::query()->first()->update(['isLocked'=>false]);
        return redirect()->back();
    }



    // MatchingController.php
    public function resetSession()
    {
        // Hapus data no_dn dan no_job dari session
        Session::forget('slip_barcode');
        Session::forget('slip_no');
        Session::forget('part_no');
        Session::forget('part_name');
        Session::forget('del_date');
        Session::forget('order_kbn');
        Session::forget('match_kbn');
        Session::forget('dn_status');
        Session::forget('pcc_seq');
        Session::forget('barcode_fg');
        Session::forget('part_no_fg');
        Session::forget('no_seq_fg');
        Session::forget('lot_no_fg');

        // Redirect kembali ke halaman input dengan pesan
        return redirect()->back()->with('message-reset', 'Session has been reset.');
    }
}
