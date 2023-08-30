<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class KopiExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('kopis');
        $documents = $collectionReference->documents();

        $data = [];

        foreach ($documents as $doc) {
            $documentData = $doc->data();

            $id = $doc->id();
            $jenis = $documentData['jenis'] ?? null;
            $foto = $documentData['foto'] ?? null;
            $deskripsi = $documentData['deskripsi'] ?? null;


            $data[] = [
                'id' => $id,
                'jenis' => $jenis,
                'foto' => $foto,
                'deskripsi' => $deskripsi,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['ID', 'Jenis', 'Foto', 'Deskripsi'];
    }
}
