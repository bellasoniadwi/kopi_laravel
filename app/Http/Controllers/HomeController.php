<?php

namespace App\Http\Controllers;

use App\Exports\KehadiranExport;
use Google\Cloud\Firestore\FirestoreClient;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('records');
        $documents = $collectionReference->documents();

        $markers = [];

        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $latitude = $documentData['latitude'] ?? null;
            $longitude = $documentData['longitude'] ?? null;
            $jenis = $documentData['jenis'] ?? null;

            if ($latitude !== null && $longitude !== null) {
                $markers[] = [
                    'latLng' => [$latitude, $longitude],
                    'style' => [
                        'fill' => 'rgb(255, 0, 127)',
                    ],
                    'jenis' => $jenis,
                ];
            }
        }
        return view('pages.dashboard', compact('markers'));
    }

    public function notauthorize()
    {
        return view('newlayout.authorization');
    }
}
