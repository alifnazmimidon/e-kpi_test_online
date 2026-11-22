<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('editprofile', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'name'            => ['required', 'string', 'min:2', 'max:100'],
            'email'           => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->worker_id, 'worker_id'),
            ],
            'phone'           => ['nullable', 'string', 'max:25', 'regex:/^\+?[0-9\s\-()]{8,20}$/'],
            'positions'       => ['nullable', 'array', 'max:20'],
            'positions.*'     => ['string', 'min:2', 'max:60'],
            'profile_pic_url' => ['nullable', 'string'],
        ]);

        // 1. Nama penuh & emel (kolum wajib jadual users)
        $user->full_name = $validated['name'];
        $user->email     = $validated['email'];

        // 2. Nombor telefon (jika kolum wujud)
        if (Schema::hasColumn('users', 'phone')) {
            $user->phone = $validated['phone'] ?? null;
        } elseif (Schema::hasColumn('users', 'phone_number')) {
            $user->phone_number = $validated['phone'] ?? null;
        }

        // 3. Sejarah jawatan & jawatan terkini
        // 3. Sejarah jawatan & jawatan terkini
        $positions = array_values(array_filter($validated['positions'] ?? [], fn($p) => trim($p) !== '' && trim($p) !== '[]'));
        if (empty($positions) && !empty($user->designation)) {
            $positions = [$user->designation];
        }
        if (Schema::hasColumn('users', 'positions')) {
            $user->positions = count($positions) > 0 ? json_encode($positions) : null;
        }
        if (Schema::hasColumn('users', 'designation') && count($positions) > 0) {
            $user->designation = end($positions);
        }

        // 4. Muat naik atau padam gambar profil dengan pengesahan binari
        $photoColumn = Schema::hasColumn('users', 'profile_photo')
            ? 'profile_photo'
            : (Schema::hasColumn('users', 'profile_pic_url') ? 'profile_pic_url' : null);

        $finalPhotoUrl = $photoColumn ? ($user->{$photoColumn} ?? '') : '';

        if ($photoColumn && $request->has('profile_pic_url')) {
            $rawImg = (string) $request->input('profile_pic_url');

            if ($rawImg === '') {
                if (!empty($user->{$photoColumn}) && Storage::disk('public')->exists($user->{$photoColumn})) {
                    Storage::disk('public')->delete($user->{$photoColumn});
                }
                $user->{$photoColumn} = null;
                $finalPhotoUrl = '';
            } elseif (preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/i', $rawImg, $matches)) {
                $ext = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
                $binary = base64_decode($matches[2], true);

                if ($binary === false || strlen($binary) > 2 * 1024 * 1024 || @getimagesizefromstring($binary) === false) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors'  => ['profile_pic_url' => ['Fail gambar tidak sah atau melebihi 2MB.']],
                    ], 422);
                }

                if (!empty($user->{$photoColumn}) && Storage::disk('public')->exists($user->{$photoColumn})) {
                    Storage::disk('public')->delete($user->{$photoColumn});
                }

                $filename = 'avatars/' . $user->worker_id . '_' . time() . '.' . $ext;
                Storage::disk('public')->put($filename, $binary);

                $user->{$photoColumn} = $filename;
                $finalPhotoUrl = asset('storage/' . $filename);
            }
        } elseif ($finalPhotoUrl && !str_starts_with($finalPhotoUrl, 'http') && !str_starts_with($finalPhotoUrl, '/')) {
            $finalPhotoUrl = asset('storage/' . ltrim($finalPhotoUrl, '/'));
        }

        $user->save();

        return response()->json([
            'message'         => 'Profile details updated and saved!',
            'positions'       => array_values($positions),
            'profile_pic_url' => $finalPhotoUrl,
        ]);
    }
}