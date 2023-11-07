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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'string',
        ]);

        return Note::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
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
        if ($note->user_id !== Auth::id()) {
            // Returns 404 instead of 403 to prevent learning what notes exist.
            abort(404);
        }

        if ($request->isMethod('PUT')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'string',
            ]);
        } else {
            $request->validate([
                'title' => 'string|max:255',
                'content' => 'string',
            ]);
        }

        if ($request->isMethod('PUT') || $request->title) {
            $note->title = $request->title;
        }
        if ($request->isMethod('PUT') || $request->content) {
            $note->content = $request->content;
        }
        $note->save();

        return $note;
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Note $note)
    {
        //
    }
}
