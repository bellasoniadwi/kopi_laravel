<?php

namespace App\Http\Controllers;

use App\Exports\RecordExport;
use Google\Cloud\Firestore\FirestoreClient;
use Maatwebsite\Excel\Facades\Excel;
use Kreait\Firebase\Contract\Firestore;
use App\Exports\StudentsExport;
use DateTime;
use Google\Cloud\Core\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use RealRashid\SweetAlert\Facades\Alert;

class RecordController extends Controller
{
    public function index()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('records');
        $data = [];

        $documents = $collectionReference->documents();

            foreach ($documents as $doc) {

                $documentData = $doc->data();
                $documentId = $doc->id();

                $jenis = $documentData['jenis'] ?? null;
                $timestamps = $documentData['timestamps'] ?? null;
                $deskripsi = $documentData['deskripsi'] ?? null;
                $foto = $documentData['foto'] ?? null;
                $latitude = $documentData['latitude'] ?? null;
                $longitude = $documentData['longitude'] ?? null;
                $googleMapsUrl = sprintf('https://www.google.com/maps?q=%f,%f', $latitude, $longitude);
                $feedback = $documentData['feedback'] ?? null;

                $data[] = [
                    'jenis' => $jenis,
                    'deskripsi' => $deskripsi,
                    'timestamps' => $timestamps,
                    'foto' => $foto,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'googleMapsUrl' => $googleMapsUrl,
                    'id'=>$documentId,
                    'feedback'=> $feedback
                ];

            }
        return view('pages.records', compact('data'));
    }

    public function create_form() {
        return view('pages.record_form');
    }

    public function edit_form($documentId) {
        try {
            $record = app('firebase.firestore')->database()->collection('records')->document($documentId)->snapshot();

            return view('pages.record_edit_form', compact('record', 'documentId'));
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal mengambil data record: ' . $e->getMessage()], 500);
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
            $recordRef = $firestore->database()->collection('records');

            $recordRef->add([
                'jenis' => $request->input('jenis'),
                'deskripsi' => $request->input('deskripsi'),
                'foto' => $imagePath,
            ]);
            
            Alert::success('Data record berhasil ditambahkan');
            return redirect()->route('record');
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
                $recordRef = $firestore->database()->collection('records')->document($documentId)->snapshot();
                $imagePath = $recordRef->get('foto');
            }
        
                $firestore = app(Firestore::class);
                $recordRef = $firestore->database()->collection('records')->document($documentId);

                $recordRef->update([
                    ['path' => 'jenis', 'value' => $request->input('jenis')],
                    ['path' => 'deskripsi', 'value' => $request->input('deskripsi')],
                    ['path' => 'foto', 'value' => $imagePath],
                    ['path' => 'feedback', 'value' => $request->input('feedback')],
                ]);

                Alert::success('Feedback berhasil ditambahkan');
                return redirect()->route('record');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }
    
    public function delete($documentId)
    {
        try {
            app('firebase.firestore')->database()->collection('records')->document($documentId)->delete();
            Alert::success('Data record berhasil dihapus');
            return redirect()->route('siswa');
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal menghapus data record: ' . $e->getMessage()], 500);
        }
    }
    
    //export excel untuk data bukti kehadiran siswa
    public function exportExcel()
    {
        return Excel::download(new RecordExport(), 'record_kopi.xlsx');
    }
}
