<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Contract\Firestore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use PhpOffice\PhpSpreadsheet\IOFactory as PHPExcel_IOFactory;

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
                'id'=>$documentId
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

    //START FUNCTION EDIT

    public function validator_edit(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    public function edit_form($documentId) {
        $userCollection = app('firebase.firestore')->database()->collection('users');

        $userDocuments = $userCollection->documents();
        $list_user = [];
        foreach ($userDocuments as $document) {
            $list_user[] = $document->data();
        }

        try {
            $user = app('firebase.firestore')->database()->collection('users')->document($documentId)->snapshot();

            return view('pages.user_edit_form', compact('user', 'documentId', 'list_user'));
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal mengambil data user: ' . $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $documentId)
    {
        try{
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

            $this->validator_edit($request->all())->validate();

                $firestore = app(Firestore::class);
                $userRef = $firestore->database()->collection('users')->document($documentId);

                if($role_akun == 'Superadmin'){
                    $userRef->update([
                        ['path' => 'name', 'value' => $request->input('name')],
                        ['path' => 'role', 'value' => $request->input('role')],
                    ]);
                } elseif($role_akun == 'Pengawas'){
                    $userRef->update([
                        ['path' => 'name', 'value' => $request->input('name')],
                    ]);
                }
                

                Alert::success('Data akun pengguna berhasil diubah');
                return redirect()->route('user.index');
        } catch (FirebaseException $e) {
            Session::flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    //END FUNCTION EDIT


    // START FUNCTION DELETE FOR FIRESTORE COLLECTIONS USERS AND USERS AUTHENTICATION
    public function delete($documentId, Auth $firebaseAuth)
    {
        try {
            // Get the user's email from Firestore
            $userDocument = app('firebase.firestore')->database()->collection('users')->document($documentId)->snapshot();
            $userEmail = $userDocument->data()['email'];

            // Delete the user document from Firestore
            app('firebase.firestore')->database()->collection('users')->document($documentId)->delete();

            // Delete the user's authentication record from Firebase Authentication
            $user = $firebaseAuth->getUserByEmail($userEmail);
            $firebaseAuth->deleteUser($user->uid);

            Alert::success('Data akun pengguna berhasil dihapus');
            return redirect()->route('user.index');
        } catch (FirebaseException $e) {
            return response()->json(['message' => 'Gagal menghapus data akun pengguna: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    // END FUNCTION DELETE FOR FIRESTORE COLLECTIONS USERS AND USERS AUTHENTICATION

    public function exportExcel()
    {
        return Excel::download(new UsersExport(), 'users.xlsx');
    }

    public function importExcel(Request $request)
    {
        $uploadedFile = $request->file('users_excel');

        // Load the Excel file
        $objPHPExcel = PHPExcel_IOFactory::load($uploadedFile);
        $worksheet = $objPHPExcel->getActiveSheet();

        // Initialize Firestore
        $firestore = new FirestoreClient([
            'projectId' => 'kopi-sinarindo',
        ]);

        // Get all rows starting from the 2nd row (assuming the 1st row is headers)
        $excelData = $worksheet->toArray(null, true, true, true);
        $skipFirstRow = true;

        // Iterate through each row and add it to Firestore
        foreach ($excelData as $rowData) {
            if ($skipFirstRow) {
                $skipFirstRow = false;
                continue;
            }

            $email = $rowData['B'];

            // Cek email telah terpakai/belum
            $existingUser = $this->findUserByEmail($firestore, $email);

            // email belum terpakai
            if (!$existingUser) {
                $firebaseData = [
                    'name' => $rowData['A'],
                    'email' => $email,
                    'password' => $rowData['B'],
                    'role' => $rowData['C'],
                ];

                // Add the data to Firestore
                $createdUser = $this->auth->createUser($firebaseData);

                // Specify the Firestore collection
                $collection = $firestore->collection('users')->document($createdUser->uid);
                $collection->set([
                    'name' => $rowData['A'],
                    'email' => $email,
                    'role' => $rowData['C'],
                ]);
            }
        }
        return redirect()->back()->with('success', 'Data akun berhasil diimport');
    }

    private function findUserByEmail($firestore, $email)
    {
        $query = $firestore->collection('users')->where('email', '=', $email);
        $documents = $query->documents();

        foreach ($documents as $document) {
            return $document;
        }

        return null;
    }
}
