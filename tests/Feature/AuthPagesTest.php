<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_in_page_has_password_toggle_and_autocomplete(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('data-toggle-password="#password"', false)
            ->assertSee('autocomplete="current-password"', false)
            ->assertSee('data-loading', false)
            ->assertSee('Welcome back')
            ->assertSee(route('sign_up'), false)
            ->assertDontSee('auth-side', false);
    }

    public function test_sign_up_page_has_strength_meter_and_match_check(): void
    {
        $this->get(route('sign_up'))
            ->assertOk()
            ->assertSee('id="password-strength"', false)
            ->assertSee('data-matches="#password"', false)
            ->assertSee('autocomplete="new-password"', false)
            ->assertSee('Create your account')
            ->assertSee('name="terms"', false);
    }

    public function test_admin_login_page_renders(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Administrators only')
            ->assertSee('data-toggle-password="#password"', false);
    }

    public function test_wrong_sign_in_shows_the_error_under_the_email_field(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login.submit'), ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertSee('id="email-error"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('Email or password is wrong.')
            ->assertDontSee('Please fix the following:')
            ->assertSee('value="'.$user->email.'"', false);
    }

    public function test_sign_up_errors_show_under_each_field_and_keep_old_input(): void
    {
        $this->from(route('sign_up'))
            ->post(route('sign_up.submit'), [
                'name' => 'Sara',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'other',
            ])
            ->assertRedirect(route('sign_up'));

        $this->get(route('sign_up'))
            ->assertSee('id="email-error"', false)
            ->assertSee('id="password-error"', false)
            ->assertSee('value="Sara"', false)
            ->assertDontSee('value="short"', false);
    }

    public function test_auth_pages_render_in_arabic(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->get(route('sign_up'))
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('إظهار كلمة المرور');
    }
}
