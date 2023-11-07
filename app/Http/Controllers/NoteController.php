<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Return a listing of the user’s notes.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified note.
     */
    public function show(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            // Returns 404 instead of 403 to prevent learning what notes exist.
            abort(404);
        }

        return $note;
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Note $note)
    {
        //
    }
}
