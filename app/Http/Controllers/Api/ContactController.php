<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ContactController extends Controller
{
    #[OA\Post(path: "/api/contact", summary: "Submit contact inquiry", tags: ["Contact"])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["full_name", "email", "phone", "message"],
            properties: [
                new OA\Property(property: "full_name", type: "string"),
                new OA\Property(property: "email", type: "string", format: "email"),
                new OA\Property(property: "phone", type: "string"),
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Inquiry submitted successfully")]
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        $contact = Contact::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for contacting us. We will get back to you soon!',
            'data' => $contact
        ], 201);
    }
}
