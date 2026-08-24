<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
        Storage::fake('public');
    }

    public function test_profile_page_renders(): void
    {
        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertViewIs('modules.profile.edit')
            ->assertSee('Mi perfil');
    }

    public function test_password_can_be_changed(): void
    {
        $user = auth()->user();

        $this->from(route('profile.edit'))->patch(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_password_change_rejects_wrong_current_password(): void
    {
        $user = auth()->user();

        $this->from(route('profile.edit'))->patch(route('profile.password'), [
            'current_password' => 'not-my-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_password_change_requires_confirmation(): void
    {
        $this->patch(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'different-confirmation',
        ])->assertSessionHasErrors('password');
    }

    public function test_photo_can_be_uploaded(): void
    {
        $user = auth()->user();

        $this->post(route('profile.photo'), [
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ])->assertRedirect()
            ->assertSessionHas('success');

        $photo = $user->fresh()->photo;

        $this->assertNotNull($photo);
        $this->assertStringStartsWith('avatars/', $photo);
        Storage::disk('public')->assertExists($photo);
    }

    public function test_uploading_a_new_photo_deletes_the_previous_file(): void
    {
        $user = auth()->user();

        $this->post(route('profile.photo'), [
            'photo' => UploadedFile::fake()->image('first.png'),
        ]);

        $firstPhoto = $user->fresh()->photo;

        $this->post(route('profile.photo'), [
            'photo' => UploadedFile::fake()->image('second.png'),
        ]);

        $secondPhoto = $user->fresh()->photo;

        $this->assertNotSame($firstPhoto, $secondPhoto);
        Storage::disk('public')->assertMissing($firstPhoto);
        Storage::disk('public')->assertExists($secondPhoto);
    }

    public function test_photo_can_be_removed(): void
    {
        $user = auth()->user();

        $this->post(route('profile.photo'), [
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $photo = $user->fresh()->photo;

        $this->delete(route('profile.photo.destroy'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertNull($user->fresh()->photo);
        Storage::disk('public')->assertMissing($photo);
    }

    public function test_removing_a_missing_photo_does_not_fail(): void
    {
        $this->delete(route('profile.photo.destroy'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertNull(auth()->user()->fresh()->photo);
    }

    public function test_photo_must_be_an_image(): void
    {
        $this->post(route('profile.photo'), [
            'photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('photo');

        $this->assertNull(auth()->user()->fresh()->photo);
    }

    public function test_photo_must_not_exceed_two_megabytes(): void
    {
        $this->post(route('profile.photo'), [
            'photo' => UploadedFile::fake()->image('huge.jpg')->size(3000),
        ])->assertSessionHasErrors('photo');

        $this->assertNull(auth()->user()->fresh()->photo);
    }
}
