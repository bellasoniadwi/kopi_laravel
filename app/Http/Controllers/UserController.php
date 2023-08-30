<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Contract\Firestore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        if ($user) {
            $id = $user->localId;

            $firestore = app('firebase.firestore');
            $database = $firestore->database();

            $userDocRef = $database->collection('users')->document($id);
            $userSnapshot = $userDocRef->snapshot();

            if ($userSnapshot->exists()) {
                $nama_akun = $userSnapshot->data()['name'];
                $role_akun = $userSnapshot->data()['role'];
            } else {
                $nama_akun = "Name not found";
                $role_akun = "Role not found";
            }
        } else {
            $nama_akun = "Name ga kebaca";
            $role_akun = "Role ga kebaca";
        }

        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        $collectionReference = $firestore->collection('users');
        $data = [];

        if ($role_akun == 'Superadmin') {
            $query = $collectionReference->orderBy('name');
        } elseif ($role_akun == 'Pengawas') {
            $query = $collectionReference->where('role','=','Petani')->orderBy('name', 'asc');
        } else {
            $query = $collectionReference->orderBy('name');
        }

        $documents = $query->documents();

        foreach ($documents as $doc) {

            $documentData = $doc->data();
            $documentId = $doc->id();

            $name = $documentData['name'] ?? null;
            $email = $documentData['email'] ?? null;
            $role = $documentData['role'] ?? null;

            $data[] = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ];
        }

        return view('pages.users', compact('data'));
    }


    public function create_form() {
        return view('pages.user_form');
    }

    protected $auth;

    public function __construct(Auth $auth) {
       $this->auth = $auth;
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            // 'role' => ['required', 'string', 'max:255'],
        ]);
    }

    public function create(Request $request) {
        try {
            $user = auth()->user();

            if ($user) {
                $id = $user->localId;
                $firestore = app('firebase.firestore');
                $database = $firestore->database();
    
                $userDocRef = $database->collection('users')->document($id);
                $userSnapshot = $userDocRef->snapshot();
    
                if ($userSnapshot->exists()) {
                    $name = $userSnapshot->data()['name'];
                    $role_akun = $userSnapshot->data()['role'];
                } else {
                    $name = "Tidak Dikenali";
                }
            } else {
                $name = "Tidak Dikenali";
            }

            $this->validator($request->all())->validate();

                       if ($role_akun == 'Superadmin') {
                $userProperties = [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'name' => $request->input('name'),
                    'role' => $request->input('role'),
                ];
      
                $createdUser = $this->auth->createUser($userProperties);
    
                $firestore = app(Firestore::class);
                $userRef = $firestore->database()->collection('users')->document($createdUser->uid);
                $userRef->set([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => $request->input('role'),
                ]);
    
                Alert::success('Akun baru berhasil ditambahkan');
                return redirect()->route('user.index');
            } elseif($role_akun == 'Pengawas'){
                $userProperties = [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'name' => $request->input('name'),
                    'role' => 'Petani',
                ];
      
                $createdUser = $this->auth->createUser($userProperties);
    
                $firestore = app(Firestore::class);
                $userRef = $firestore->database()->collection('users')->document($createdUser->uid);
                $userRef->set([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 'Petani',
                ]);
    
                Alert::success('Akun baru berhasil ditambahkan');
                return redirect()->route('user.index');
            }
            
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }
}
