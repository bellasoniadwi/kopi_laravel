<?php

namespace App\Http\Controllers;

use App\Exports\KopiExport;
use Google\Cloud\Firestore\FirestoreClient;
use Maatwebsite\Excel\Facades\Excel;
use Kreait\Firebase\Contract\Firestore;
use App\Exports\StudentsExport;
use App\Helpers\Helper;
use DateTime;
use Google\Cloud\Core\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use RealRashid\SweetAlert\Facades\Alert;

class KopiController extends Controller
{
    public function index()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('kopis');
        $data = [];

        $documents = $collectionReference->documents();

            foreach ($documents as $doc) {

                $documentData = $doc->data();
                $documentId = $doc->id();

                $jenis = $documentData['jenis'] ?? null;
                $deskripsi = $documentData['deskripsi'] ?? null;
                $foto = $documentData['foto'] ?? null;

                $data[] = [
                    'jenis' => $jenis,
                    'deskripsi' => $deskripsi,
                    'foto' => $foto,
                    'id'=>$documentId
                ];

            }
        return view('pages.kopi', compact('data'));
    }

    public function create_form() {
        return view('pages.kopi_form');
    }

    public function edit_form($documentId) {
        try {
            $kopi = app('firebase.firestore')->database()->collection('kopis')->document($documentId)->snapshot();

            return view('pages.kopi_edit_form', compact('kopi', 'documentId'));
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal mengambil data kopi: ' . $e->getMessage()], 500);
        }
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'jenis' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'foto' => ['mimes:png,jpg,jpeg', 'max:2048']
        ]);
    }

    public function create(Request $request) {
        try {
    
            $this->validator($request->all())->validate();
    
            if ($request->hasFile('foto')) {
                $imageFile = $request->file('foto');

                $storage = Firebase::storage();
                $uniqueId = microtime(true) * 10000;
                $storagePath = 'images/' . $uniqueId . '_' . now()->format('Y-m-d') . '.jpg';

                $storage->getBucket()->upload(
                    file_get_contents($imageFile->getRealPath()),
                    ['name' => $storagePath]
                );

                $imagePath = $storage->getBucket()->object($storagePath)->signedUrl(now()->addYears(10));
            } else {
                $imagePath = null;
            }
    
            $firestore = app(Firestore::class);
            $kopiRef = $firestore->database()->collection('kopis')->document(Helper::IdKopiGenerator());
            $kopiRef->set([
                'jenis' => $request->input('jenis'),
                'deskripsi' => $request->input('deskripsi'),
                'foto' => $imagePath,
            ]);
            
            Alert::success('Data kopi berhasil ditambahkan');
            return redirect()->route('kopi');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    public function update(Request $request, $documentId)
    {
        try{
            $this->validator($request->all())->validate();
        
            if ($request->hasFile('foto')) {
                $imageFile = $request->file('foto');

                $storage = Firebase::storage();
                $uniqueId = microtime(true) * 10000;
                $storagePath = 'images/' . $uniqueId . '_' . now()->format('Y-m-d') . '.jpg';

                $storage->getBucket()->upload(
                    file_get_contents($imageFile->getRealPath()),
                    ['name' => $storagePath]
                );

                $imagePath = $storage->getBucket()->object($storagePath)->signedUrl(now()->addYears(10));
            } else {
                $firestore = app(Firestore::class);
                $kopiRef = $firestore->database()->collection('kopis')->document($documentId)->snapshot();
                $imagePath = $kopiRef->get('foto');
            }
        
                $firestore = app(Firestore::class);
                $kopiRef = $firestore->database()->collection('kopis')->document($documentId);

                $kopiRef->update([
                    ['path' => 'jenis', 'value' => $request->input('jenis')],
                    ['path' => 'deskripsi', 'value' => $request->input('deskripsi')],
                    ['path' => 'foto', 'value' => $imagePath],
                ]);

                Alert::success('Data kopi berhasil diubah');
                return redirect()->route('kopi');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }
    
    public function delete($documentId)
    {
        try {
            app('firebase.firestore')->database()->collection('kopis')->document($documentId)->delete();
            Alert::success('Data kopi berhasil dihapus');
            return redirect()->route('kopi');
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal menghapus data kopi: ' . $e->getMessage()], 500);
        }
    }
    
    //export excel untuk data bukti kehadiran siswa
    public function exportExcel()
    {
        return Excel::download(new KopiExport(), 'data_kopi.xlsx');
    }
}
