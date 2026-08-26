<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->password = $newPassword;
        $user->save();
    }

    public function updatePhoto(User $user, UploadedFile $file): void
    {
        $path = $file->store('avatars', 'public');

        $this->deletePhotoFile($user);

        $user->photo = $path;
        $user->save();
    }

    public function removePhoto(User $user): void
    {
        $this->deletePhotoFile($user);

        $user->photo = null;
        $user->save();
    }

    private function deletePhotoFile(User $user): void
    {
        if (! empty($user->photo) && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
    }
}
