<?php
namespace App\Http\Controllers;
use App\Models\Contact;
use Illuminate\Http\Request;
class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        if (Contact::where('email', $validatedData['email'])->exists()) {
            return back()->withErrors([
                'email' => 'Cette adresse e-mail a déjà été utilisée pour envoyer un message.',
            ]);
        }
        Contact::create($validatedData);
        return back()->with('success', 'Votre message a bien été envoyé. Merci !');
    }
}
