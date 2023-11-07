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
        return Note::where('user_id', Auth::id())->paginate(20);
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
        $this->assureOwnership($note);

        return $note;
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note)
    {
        $this->assureOwnership($note);

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
        $this->assureOwnership($note);

        $note->delete();

        return response()->noContent();
    }

    /**
     * Assure a user owns the note.
     *
     * Aborts with 404 status otherwise.
     *
     * 404 was chosen instead of 403 to prevent malicious actors from learning what notes exist.
     */
    public function assureOwnership(Note $note): void
    {
        if ($note->user_id !== Auth::id()) {
            abort(404);
        }
    }
}
