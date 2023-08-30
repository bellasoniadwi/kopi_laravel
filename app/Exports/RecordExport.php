<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class RecordExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('records');
        $documents = $collectionReference->documents();

        $data = [];
        foreach ($documents as $doc) {
            $documentData = $doc->data();
            $id = $doc->id();
            $tanggal = date('Y-m-d', strtotime($documentData['timestamps'])) ?? null;
            $timestamp = new \DateTime($documentData['timestamps']);
            $timezone = new \DateTimeZone('Asia/Jakarta');
            $timestamp->setTimezone($timezone);
            $waktu = $timestamp->format('H:i:s');
            $jenis = $documentData['jenis'] ?? null;
            $foto = $documentData['foto'] ?? null;
            $latitude = $documentData['latitude'] ?? null;
            $longitude = $documentData['longitude'] ?? null;
            $googleMapsUrl = sprintf('https://www.google.com/maps?q=%f,%f', $latitude, $longitude);
            $deskripsi = $documentData['deskripsi'] ?? null;
            $feedback = $documentData['feedback'] ?? null;


            $data[] = [
                'id' => $id,
                'tanggal' => $tanggal,
                'waktu' => $waktu,
                'jenis' => $jenis,
                'foto' => $foto,
                'lokasi' => $googleMapsUrl,
                'deskripsi' => $deskripsi,
                'feedback' => $feedback,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['ID', 'Tanggal', 'Waktu', 'Jenis', 'Foto', 'Lokasi', 'Deskripsi', 'Feedback'];
    }
}