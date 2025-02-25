<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\reviewRequest;
use App\Models\Book;
use Illuminate\Routing\Controller;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Book $book)
    {
        return view('books.reviews.create',['book' => $book]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(reviewRequest $request,Book $book)
    {
        $data = $request->validated();
      
        $book->reviews()->create($data);
    
        
        return redirect()->route('books.show',$book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}