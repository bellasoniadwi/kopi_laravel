<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class Firebase extends Model
{
    //Model ini digunakan untuk menginisialisasi collection firestore "students"
    use HasFactory;

    protected $table = 'students';
    protected $fillable = [
        'name', 'nim', 'angkatan', 'timestamps', 'image', 'latitude', 'longitude'
    ];

    protected $firestore;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);
    }
}
