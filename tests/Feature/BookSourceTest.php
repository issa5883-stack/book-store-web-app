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

class BookSourceTest extends TestCase
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
    private function bookPayload(Category $category, array $overrides = []): array
    {
        return array_merge([
            'title' => 'تعلم Python للمبتدئين',
            'author' => 'Sara Ahmad',
            'description' => 'A beginner friendly book.',
            'category_id' => $category->id,
            'pdf' => UploadedFile::fake()->create('book.pdf', 200, 'application/pdf'),
        ], $overrides);
    }

    public function test_a_user_upload_is_a_pending_user_book_even_if_the_form_is_tampered_with(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Programming']);

        $this->actingAs($user)->post(route('my-books.store'), $this->bookPayload($category, [
            'status' => Book::STATUS_APPROVED,
            'source' => Book::SOURCE_ADMIN,
            'admin_id' => 1,
        ]))->assertRedirect(route('my-books.index'));

        $book = Book::sole();
        $this->assertSame(Book::STATUS_PENDING, $book->status);
        $this->assertSame(Book::SOURCE_USER, $book->source);
        $this->assertSame($category->id, $book->category_id);
        $this->assertNull($book->admin_id);
    }

    public function test_editing_a_rejected_request_cannot_change_its_status_or_source(): void
    {
        $book = Book::factory()->rejected()->create();

        $this->actingAs($book->user)->put(route('my-books.update', $book), [
            'title' => 'Fixed',
            'author' => $book->author,
            'description' => $book->description,
            'status' => Book::STATUS_APPROVED,
            'source' => Book::SOURCE_ADMIN,
        ])->assertRedirect(route('my-books.index'));

        $book->refresh();
        $this->assertSame(Book::STATUS_PENDING, $book->status);
        $this->assertSame(Book::SOURCE_USER, $book->source);
    }

    public function test_an_admin_upload_is_a_published_library_book(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create();

        $this->withSession(['admin_id' => $admin->id])
            ->post(route('admin.books.store'), $this->bookPayload($category, ['source' => Book::SOURCE_USER]))
            ->assertRedirect(route('admin.books.index'));

        $book = Book::sole();
        $this->assertSame(Book::STATUS_APPROVED, $book->status);
        $this->assertSame(Book::SOURCE_ADMIN, $book->source);
    }

    public function test_users_cannot_approve_books_or_edit_library_books(): void
    {
        $user = User::factory()->create();
        $ownRequest = Book::factory()->pending()->create(['user_id' => $user->id]);
        $libraryBook = Book::factory()->create();

        $this->actingAs($user)->patch(route('admin.requests.approve', $ownRequest))->assertRedirect(route('admin.login'));
        $this->assertSame(Book::STATUS_PENDING, $ownRequest->refresh()->status);

        $this->actingAs($user)->get(route('my-books.edit', $libraryBook))->assertForbidden();
        $this->actingAs($user)->delete(route('my-books.destroy', $libraryBook))->assertForbidden();
    }

    public function test_an_approved_user_book_appears_in_its_category_and_in_user_books(): void
    {
        $programming = Category::factory()->create(['name' => 'Programming']);
        $history = Category::factory()->create(['name' => 'History']);
        $python = Book::factory()->pending()->for($programming)->create(['title' => 'تعلم Python للمبتدئين']);
        Book::factory()->fromUser()->for($history)->create(['title' => 'كتاب في التاريخ الإسلامي']);
        Book::factory()->for($programming)->create(['title' => 'Library Programming Book']);

        $this->withSession(['admin_id' => $this->admin()->id])->patch(route('admin.requests.approve', $python));
        $this->assertSame(Book::SOURCE_USER, $python->refresh()->source);
        $this->assertSame(Book::STATUS_APPROVED, $python->status);

        $this->get(route('books.index', ['category' => $programming->id]))
            ->assertSee('تعلم Python للمبتدئين')
            ->assertSee('Library Programming Book')
            ->assertDontSee('كتاب في التاريخ الإسلامي');

        $this->get(route('books.index', ['source' => 'user']))
            ->assertSee('Books by users')
            ->assertSee('تعلم Python للمبتدئين')
            ->assertSee('كتاب في التاريخ الإسلامي')
            ->assertDontSee('Library Programming Book');

        $this->get(route('books.index', ['source' => 'user', 'category' => $history->id]))
            ->assertSee('كتاب في التاريخ الإسلامي')
            ->assertDontSee('تعلم Python للمبتدئين');
    }

    public function test_pending_and_rejected_user_books_never_appear_publicly(): void
    {
        Book::factory()->pending()->create(['title' => 'Waiting Request']);
        Book::factory()->rejected()->create(['title' => 'Rejected Request']);

        foreach ([route('home'), route('books.index'), route('books.index', ['source' => 'user'])] as $url) {
            $this->get($url)->assertOk()->assertDontSee('Waiting Request')->assertDontSee('Rejected Request');
        }

        $this->get(route('home'))->assertSee('No user books yet');
    }

    public function test_home_page_keeps_user_books_in_their_own_section(): void
    {
        Book::factory()->create(['title' => 'Library Classic', 'downloads' => 10]);
        Book::factory()->fromUser()->create(['title' => 'Member Upload', 'downloads' => 999]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Popular books', 'Library Classic', 'Latest books', 'Books by users', 'View all user books', 'Member Upload', 'Browse categories'])
            ->assertSee(route('books.index', ['source' => 'user']), false);

        $popularSection = str($this->get(route('home'))->getContent())->between('id="popular"', 'id="latest"');
        $this->assertStringNotContainsString('Member Upload', $popularSection);
    }

    public function test_an_unknown_source_filter_is_ignored(): void
    {
        Book::factory()->create(['title' => 'Library Classic']);
        Book::factory()->fromUser()->create(['title' => 'Member Upload']);

        $this->get(route('books.index', ['source' => 'hacked']))
            ->assertOk()
            ->assertSee('Library Classic')
            ->assertSee('Member Upload');
    }

    public function test_search_matches_arabic_category_names(): void
    {
        $programming = Category::factory()->create(['name' => 'Programming']);
        Book::factory()->for($programming)->create(['title' => 'Clean Code']);
        Book::factory()->create(['title' => 'Unrelated Title']);

        $this->withSession(['locale' => 'ar'])
            ->get(route('books.index', ['search' => 'البرمجة']))
            ->assertSee('Clean Code')
            ->assertDontSee('Unrelated Title');
    }
}
