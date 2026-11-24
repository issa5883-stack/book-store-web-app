<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_search_books_categories_and_authors(): void
    {
        $category = Category::factory()->create(['name' => 'Philosophy']);
        Book::factory()->for($category)->create(['title' => 'Most Read Book', 'author' => 'Popular Writer', 'downloads' => 900]);
        Book::factory()->for($category)->create(['title' => 'Brand New Book', 'downloads' => 0]);
        Book::factory()->pending()->create(['title' => 'Hidden Pending Book', 'author' => 'Hidden Writer']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Discover your next book')
            ->assertSee('action="'.route('books.index').'"', false)
            ->assertSeeInOrder(['Popular books', 'Most Read Book', 'Latest books', 'Brand New Book', 'Browse categories', 'Philosophy', 'Popular authors', 'Popular Writer', 'Share your book'])
            ->assertDontSee('Hidden Pending Book')
            ->assertDontSee('Hidden Writer');
    }

    public function test_home_page_shows_an_empty_state_without_books(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('No books yet')
            ->assertDontSee('Popular books');
    }

    public function test_public_pages_do_not_link_to_the_admin_area(): void
    {
        Book::factory()->create();

        foreach ([route('home'), route('books.index'), route('login'), route('sign_up')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertDontSee(route('admin.login'), false)
                ->assertDontSee(route('admin.dashboard'), false);
        }
    }

    public function test_admin_login_page_stays_reachable_directly(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee('Admin sign in');
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_search_matches_category_names(): void
    {
        $category = Category::factory()->create(['name' => 'Astronomy']);
        Book::factory()->for($category)->create(['title' => 'Stars Above', 'author' => 'Someone']);
        Book::factory()->create(['title' => 'Unrelated Title']);

        $this->get(route('books.index', ['search' => 'Astronomy']))
            ->assertOk()
            ->assertSee('Stars Above')
            ->assertDontSee('Unrelated Title');
    }
}
