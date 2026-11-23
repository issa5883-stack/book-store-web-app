<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    /**
     * Store the uploaded cover / PDF on the public disk and return the validated data
     * with the stored paths. Old files are removed when they are replaced.
     *
     * @return array<string, mixed>
     */
    protected function bookDataWithFiles(BookRequest $request, ?Book $book = null): array
    {
        $data = $request->safe()->except(['cover', 'pdf']);

        if ($request->hasFile('cover')) {
            if ($book?->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        if ($request->hasFile('pdf')) {
            if ($book?->pdf) {
                Storage::disk('public')->delete($book->pdf);
            }
            $data['pdf'] = $request->file('pdf')->store('books', 'public');
        }

        return $data;
    }
}
