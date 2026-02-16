<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $user->load('instructorProfile');

        if (!$user->instructorProfile) {
            $user->instructorProfile()->create();
            $user->load('instructorProfile');
        }

        return view('instructor.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'specialization' => ['nullable', 'string', 'max:120'],
            'dob' => ['nullable', 'date'],
        ]);

        $user = auth()->user();
        $profile = $user->instructorProfile ?: $user->instructorProfile()->create();
        $profile->update($data);

        return back()->with('status', 'Profile updated successfully.');
    }
}
