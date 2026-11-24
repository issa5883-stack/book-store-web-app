<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_is_english_and_left_to_right_by_default(): void
    {
        $this->get(route('books.index'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee('Browse the library')
            ->assertSee('العربية');
    }

    public function test_switching_to_arabic_stores_the_choice_and_returns_to_the_previous_page(): void
    {
        $this->from(route('books.index'))
            ->get(route('language.switch', 'ar'))
            ->assertRedirect(route('books.index'))
            ->assertSessionHas('locale', 'ar');

        $this->get(route('books.index'))
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('تصفّح المكتبة')
            ->assertDontSee('Browse the library')
            ->assertSee('English');
    }

    public function test_switching_back_to_english(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->get(route('language.switch', 'en'))
            ->assertSessionHas('locale', 'en');

        $this->get(route('books.index'))->assertSee('Browse the library');
    }

    public function test_unsupported_language_is_rejected(): void
    {
        $this->get(route('language.switch', 'fr'))->assertNotFound();
        $this->get(route('language.switch', 'fr'))->assertSessionMissing('locale');
    }

    public function test_every_page_renders_in_arabic(): void
    {
        $book = Book::factory()->create();
        $pending = Book::factory()->pending()->create();
        $user = User::factory()->create();

        $this->withSession(['locale' => 'ar']);

        foreach ([route('home'), route('books.index'), route('books.show', $book), route('login'), route('sign_up'), route('admin.login')] as $url) {
            $this->get($url)->assertOk()->assertSee('dir="rtl"', false);
        }

        $this->actingAs($user);
        foreach ([route('my-books.index'), route('my-books.create')] as $url) {
            $this->get($url)->assertOk()->assertSee('dir="rtl"', false);
        }

        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret123']);
        $this->withSession(['locale' => 'ar', 'admin_id' => $admin->id]);
        foreach ([route('admin.dashboard'), route('admin.books.index'), route('admin.books.create'), route('admin.books.edit', $book), route('admin.requests.index'), route('admin.requests.show', $pending), route('admin.categories.index'), route('admin.users.index')] as $url) {
            $this->get($url)->assertOk()->assertSee('dir="rtl"', false)->assertSee('لوحة التحكم');
        }
    }

    public function test_validation_and_flash_messages_are_in_arabic(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->post(route('sign_up.submit'), ['name' => '', 'email' => 'not-an-email', 'password' => 'short'])
            ->assertSessionHasErrors([
                'name' => 'حقل الاسم مطلوب.',
                'email' => 'يجب أن يكون حقل البريد الإلكتروني بريداً إلكترونياً صحيحاً.',
            ]);

        $this->withSession(['locale' => 'ar'])
            ->post(route('sign_up.submit'), [
                'name' => 'Sara',
                'email' => 'sara@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertSessionHas('success', 'مرحباً Sara! حسابك جاهز.');
    }
}
