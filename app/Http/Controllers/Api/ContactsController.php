<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\Rule;

class ContactsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ContactResource::collection(Contact::paginate(10));
    }

    /**
     * @param Contact $contact
     * @return ContactResource
     */
    public function show(Contact $contact): ContactResource
    {
        return new ContactResource($contact->loadMissing('supplier'));
    }

    /**
     * @param Request $request
     * @return ContactResource
     */
    public function store(Request $request): ContactResource
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'phone' => 'string',
            'email' => 'email',
            'role' => 'required|string|min:2|max:255',
            'notes' => 'nullable|string|min:2|max:1000',
        ]);

        $contact = new Contact($data);

        $contact->save();

        return new ContactResource($contact->loadMissing('supplier'));
    }

    /**
     * @param Contact $contact
     * @param Request $request
     * @return ContactResource
     */
    public function update(Contact $contact, Request $request): ContactResource
    {
        $data = $request->validate([
            'supplier_id' => 'exists:suppliers,id',
            'first_name' => 'string|min:2|max:255',
            'last_name' => 'string|min:2|max:255',
            'phone' => 'string',
            'email' => [
                'email',
                Rule::unique('contacts')->ignore($contact),
            ],
            'role' => 'string|min:2|max:255',
            'notes' => 'nullable|string|min:2|max:1000',
        ]);

        $contact->update($data);

        return new ContactResource($contact->loadMissing('supplier'));
    }

    /**
     * @param Contact $contact
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Contact $contact)
    {
        try {
            $contact->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
