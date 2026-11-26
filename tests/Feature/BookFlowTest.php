<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function admin(): Admin
    {
        return Admin::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret123']);
    }

    /**
     * @return array<string, mixed>
     */
    private function bookPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'My Great Book',
            'author' => 'Jane Writer',
            'description' => 'A very good book.',
            'category_id' => Category::factory()->create()->id,
            'pdf' => UploadedFile::fake()->create('book.pdf', 200, 'application/pdf'),
            'cover' => UploadedFile::fake()->image('cover.jpg'),
        ], $overrides);
    }

    public function test_public_pages_show_only_approved_books(): void
    {
        $approved = Book::factory()->create(['title' => 'Visible Book']);
        $pending = Book::factory()->pending()->create(['title' => 'Hidden Book']);

        $this->get(route('home'))->assertOk()->assertSee('Visible Book')->assertDontSee('Hidden Book');
        $this->get(route('books.index'))->assertOk()->assertSee('Visible Book')->assertDontSee('Hidden Book');
        $this->get(route('books.show', $approved))->assertOk();
        $this->get(route('books.show', $pending))->assertNotFound();
    }

    public function test_user_can_sign_up_and_sign_in(): void
    {
        $this->post(route('sign_up.submit'), [
            'name' => 'Sara',
            'email' => 'sara@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('my-books.index'));

        $this->assertAuthenticated();

        $this->post(route('logout'));
        $this->assertGuest();

        $this->post(route('login.submit'), ['email' => 'sara@test.com', 'password' => 'password123'])
            ->assertRedirect(route('my-books.index'));
        $this->assertAuthenticated();
    }

    public function test_guest_cannot_submit_books(): void
    {
        $this->get(route('my-books.create'))->assertRedirect(route('login'));
    }

    public function test_user_submission_is_pending_until_admin_approves(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('my-books.store'), $this->bookPayload())
            ->assertRedirect(route('my-books.index'));

        $book = Book::firstOrFail();
        $this->assertSame(Book::STATUS_PENDING, $book->status);
        $this->assertSame($user->id, $book->user_id);
        Storage::disk('public')->assertExists([$book->pdf, $book->cover]);

        $this->get(route('books.show', $book))->assertNotFound();

        $admin = $this->admin();
        $this->withSession(['admin_id' => $admin->id])
            ->patch(route('admin.requests.approve', $book))
            ->assertRedirect(route('admin.requests.index'));

        $book->refresh();
        $this->assertSame(Book::STATUS_APPROVED, $book->status);
        $this->assertSame($admin->id, $book->admin_id);
        $this->get(route('books.show', $book))->assertOk()->assertSee('My Great Book');
    }

    public function test_admin_can_reject_a_request_with_a_reason(): void
    {
        $book = Book::factory()->pending()->create();
        $admin = $this->admin();

        $this->withSession(['admin_id' => $admin->id])
            ->patch(route('admin.requests.reject', $book), [])
            ->assertSessionHasErrors('rejection_reason');

        $this->withSession(['admin_id' => $admin->id])
            ->patch(route('admin.requests.reject', $book), ['rejection_reason' => 'Low quality scan'])
            ->assertRedirect();

        $this->assertSame(Book::STATUS_REJECTED, $book->refresh()->status);
        $this->assertSame('Low quality scan', $book->rejection_reason);
    }

    public function test_admin_upload_is_published_immediately(): void
    {
        $admin = $this->admin();

        $this->withSession(['admin_id' => $admin->id])
            ->post(route('admin.books.store'), $this->bookPayload())
            ->assertRedirect(route('admin.books.index'));

        $book = Book::firstOrFail();
        $this->assertSame(Book::STATUS_APPROVED, $book->status);
        $this->assertNull($book->user_id);
        $this->assertSame($admin->id, $book->admin_id);
    }

    public function test_admin_area_requires_admin_session(): void
    {
        $user = User::factory()->create();

        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->get(route('admin.requests.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.books.store'), $this->bookPayload())->assertRedirect(route('admin.login'));
        $this->assertSame(0, Book::count());
    }

    public function test_admin_can_log_in_and_see_every_admin_page(): void
    {
        $this->admin();
        Book::factory()->pending()->create();

        $this->post(route('adminlogin.submit'), ['email' => 'admin@test.com', 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));

        foreach (['admin.dashboard', 'admin.requests.index', 'admin.books.index', 'admin.books.create', 'admin.categories.index', 'admin.users.index'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_user_cannot_change_books_of_other_users_or_approved_books(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->pending()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->get(route('my-books.edit', $book))->assertForbidden();
        $this->actingAs($other)->delete(route('my-books.destroy', $book))->assertForbidden();

        $book->update(['status' => Book::STATUS_APPROVED]);
        $this->actingAs($owner)->get(route('my-books.edit', $book))->assertForbidden();
    }

    public function test_editing_a_rejected_request_sends_it_back_to_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->rejected()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->put(route('my-books.update', $book), $this->bookPayload(['title' => 'Fixed Title', 'pdf' => null, 'cover' => null]))
            ->assertRedirect(route('my-books.index'));

        $book->refresh();
        $this->assertSame('Fixed Title', $book->title);
        $this->assertSame(Book::STATUS_PENDING, $book->status);
        $this->assertNull($book->rejection_reason);
    }

    public function test_download_increments_counter(): void
    {
        Storage::disk('public')->put('books/sample.pdf', '%PDF-1.4');
        $book = Book::factory()->create(['downloads' => 3]);

        $this->actingAs(User::factory()->create())
            ->get(route('books.download', $book))
            ->assertOk()
            ->assertDownload();

        $this->assertSame(4, $book->refresh()->downloads);
    }

    public function test_guest_cannot_read_or_download_a_book(): void
    {
        Storage::disk('public')->put('books/sample.pdf', '%PDF-1.4');
        $book = Book::factory()->create(['downloads' => 3]);

        $this->get(route('books.download', $book))->assertRedirect(route('login'));
        $this->get(route('books.read', $book))->assertRedirect(route('login'));

        $this->assertSame(3, $book->refresh()->downloads);
    }

    public function test_signed_in_user_can_read_a_book_online(): void
    {
        Storage::disk('public')->put('books/sample.pdf', '%PDF-1.4');
        $book = Book::factory()->create();

        $response = $this->actingAs(User::factory()->create())->get(route('books.read', $book));

        $response->assertOk();
        $this->assertStringStartsWith('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_guest_is_sent_back_to_the_book_after_signing_in(): void
    {
        Storage::disk('public')->put('books/sample.pdf', '%PDF-1.4');
        $book = Book::factory()->create();
        $user = User::factory()->create(['password' => 'secret123']);

        $this->get(route('books.download', $book))->assertRedirect(route('login'));

        $this->post(route('login.submit'), ['email' => $user->email, 'password' => 'secret123'])
            ->assertRedirect(route('books.download', $book));
    }
}
