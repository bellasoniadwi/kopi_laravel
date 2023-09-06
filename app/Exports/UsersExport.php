<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Google\Cloud\Firestore\FirestoreClient;

class UsersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('users');
        $documents = $collectionReference->documents();

        $data = [];

        foreach ($documents as $doc) {
            $documentData = $doc->data();

            $id = $doc->id();
            $name = $documentData['name'] ?? null;
            $email = $documentData['email'] ?? null;
            $role = $documentData['role'] ?? null;


            $data[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Role'];
    }
}
