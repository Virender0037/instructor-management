<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
    public function create()
    {
        return view('superadmin.instructors.create');
    }

    public function index(Request $request)
    {
        $query = User::query()->where('role', 'instructor');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (int) $request->input('status'));
        }

        if ($request->filled('verified')) {
            if ($request->input('verified') === '1') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->input('verified') === '0') {
                $query->whereNull('email_verified_at');
            }
        }

        $instructors = $query->latest()->paginate(10)->withQueryString();

        return view('superadmin.instructors.index', compact('instructors'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')],
            'telefoonnummer' => ['nullable', 'string', 'max:30'],
            'wagennummer'    => ['nullable', 'string', 'max:30'],
            'auto'           => ['nullable', 'in:Volkswagen,Mercedes,Audi'],
            'status' => ['required', 'boolean'],
        ]);

        $instructor = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(str()->random(20)),
            'role' => 'instructor',
            'status' => (int) $data['status'],
            'created_by' => $request->user()->id,
            'email_verified_at' => null,
        ]);

        $instructor->instructorProfile()->updateOrCreate(
        ['user_id' => $instructor->id],
        [
            'telefoonnummer' => $data['telefoonnummer'],
            'wagennummer'    => $data['wagennummer'],
            'auto'           => $data['auto'],
        ]
        );
        
        audit('instructor.created', $instructor, [
            'email' => $instructor->email,
        ]);

        $instructor->sendEmailVerificationNotification();

        Password::sendResetLink(['email' => $instructor->email]);

        return redirect()
            ->route('superadmin.instructors.create')
            ->with('status', 'Instructeur aangemaakt. Verificatie- en wachtwoordinstellingslinks zijn verzonden.');
    }

   public function resendVerification(User $user)
{
    if ($user->role !== 'instructor') {
        abort(404);
    }

    if ($user->email_verified_at) {
        return back()->with('status', 'De instructeur is al geverifieerd.');
    }

    $user->sendEmailVerificationNotification();
    audit('instructor.verification_resent', $user, [
        'email' => $user->email,
    ]);

    return back()->with('status', 'Verificatie-e-mail succesvol opnieuw verzonden.');
}


    public function toggleStatus(User $user)
{
    if ($user->role !== 'instructor') {
        abort(404);
    }

    $user->status = !$user->status;
    $user->save();
    audit('instructor.status_toggled', $user, [
        'status' => (int) $user->status,
    ]);

    return back()->with('status', $user->status
        ? 'Instructeur succesvol geactiveerd.'
        : 'Instructeur succesvol gedeactiveerd.'
    );
}


    public function preview(Document $document)
    {
        if ($document->owner_id !== auth()->id()) {
            abort(403);
        }

        if ($document->file_type !== 'image') {
            abort(404);
        }

        $disk = $document->storage_disk ?? 'local';

        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk($disk)->response($document->file_path);
    }

    public function editProfile(User $user)
    {
        if ($user->role !== 'instructor') abort(404);

        $user->load('instructorProfile');

        if (!$user->instructorProfile) {
            $user->instructorProfile()->create();
            $user->load('instructorProfile');
        }

        return view('superadmin.instructors.profile', compact('user'));
    }

    public function updateProfile(Request $request, User $user)
{
    if ($user->role !== 'instructor') abort(404);

    $data = $request->validate([
        'telefoonnummer' => ['nullable', 'string', 'max:30'],
        'wagennummer'    => ['nullable', 'string', 'max:30'],
        'auto'           => ['nullable', 'in:Volkswagen,Mercedes,Audi'],
        'phone' => ['nullable', 'string', 'max:30'],
        'address' => ['nullable', 'string', 'max:500'],
        'bio' => ['nullable', 'string', 'max:2000'],
        'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
        'specialization' => ['nullable', 'string', 'max:120'],
        'dob' => ['nullable', 'date'],
    ]);

    $profile = $user->instructorProfile ?: $user->instructorProfile()->create();
    $profile->update($data);

    return back()->with('status', 'Instructeursprofiel succesvol bijgewerkt.');
}


    public function edit(User $user)
    {
        if ($user->role !== 'instructor') abort(404);

        return view('superadmin.instructors.edit', compact('user'));
    }

   public function update(Request $request, User $user)
{
    if ($user->role !== 'instructor') abort(404);

    $data = $request->validate([
        'name'  => ['required', 'string', 'max:255'],
        'email' => [
            'required', 'email', 'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
    ]);

    $emailChanged = $data['email'] !== $user->email;

    $user->name = $data['name'];
    $user->email = $data['email'];

    if ($emailChanged) {
        $user->email_verified_at = null;
    }

    $user->save();

    if ($emailChanged) {
        $user->sendEmailVerificationNotification();
    }

    audit('instructor.updated', $user, [
        'email_changed' => $emailChanged,
    ]);

    return back()->with('status', $emailChanged
        ? 'Instructeur bijgewerkt. Verificatie-e-mail verzonden naar het nieuwe adres.'
        : 'Instructeur succesvol bijgewerkt.'
    );
}

public function destroy(User $user)
{
    if ($user->role !== 'instructor') {
        abort(404);
    }
    $user->delete();

    audit('instructor.soft_deleted', $user, [
        'email' => $user->email,
    ]);

    return back()->with('status', 'Instructeur is verwijderd.');
}

}
