<?php

namespace App\Helpers;
use Carbon\Carbon;
use Google\Cloud\Firestore\FirestoreClient;

class Helper {
    public static function IdKopiGenerator(){
        $now = Carbon::now();
        $month = $now->format('m');
        $year = $now->format('y');

        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('kopis');
        $documentsKopi = $collectionReference->documents();
        $dataKopi = [];

        foreach ($documentsKopi as $docKopi) {
            $documentDataKopi = $docKopi->data();
            $jenis = $documentDataKopi['jenis'] ?? null;

            $dataKopi[] = [
                'jenis' => $jenis
            ];
        }

        $totalKopis = count($dataKopi);
        $urutan = $totalKopis + 1;

        $nomor = 'BK-' . $month . $year . str_pad($urutan, 5, '0', STR_PAD_LEFT);

        return $nomor;
    }
}

