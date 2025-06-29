<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDocumentStoreRequest;
use App\Http\Requests\UserDocumentUpdateRequest;
use App\Http\Resources\UserDocumentResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UserDocument::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        $allowedSorts = ['title', 'description'];
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;

        if (in_array($sortColumn, $allowedSorts) && in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $userDocuments = UserDocumentResource::collection($query->paginate(15)->withQueryString());

        return inertia('UserDocuments/Index', [
            'userDocuments' => $userDocuments,
            'search' => $request->search,
            'sortInfo' => [
                'currentSortColumn' => $sortColumn,
                'currentSortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($user_id = null)
    {
        // If a user ID is provided, fetch the user document for that user
        if ($user_id) {
            $userDocument = UserDocument::where('user_id', $user_id)->first();
            if ($userDocument) {
                return inertia('UserDocuments/Edit', [
                    'userDocument' => new UserDocumentResource($userDocument),
                ]);
            }
        } else {
            return redirect()->back()->with('error', 'User ID is required to create a user document.');
        }

        // fetch a userDocument resource instance
        $userDocument = new UserDocumentResource(new UserDocument);

        return inertia('UserDocuments/Create');
    }

    /**
     * Add new document.
     */
    public function newDocument($user_id = null)
    {
        $user = User::find($user_id);
        if (! $user) {
            return redirect()->back()->with('error', 'User not found.');
        } else {
            $user = new UserResource($user);
        }

        // Go to userDocuments/create
        return inertia('UserDocuments/Create', [
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserDocumentStoreRequest $request)
    {
        $fields = $request->validated();

        // Handle image upload with Intervention Image
        if ($image = $request->file('image')) {
            // Prepare filename based on user name if available, else use timestamp
            $user = isset($fields['user_id']) ? User::find($fields['user_id']) : null;
            $name = $user ? preg_replace('/\s+/', '', $user->name) : 'userdoc_' . time();
            $filename = 'userdoc_' . strtolower($name) . '.png';
            $path = 'user-documents/images/' . $filename;

            // Resize and fit image to 595x842 (A4 portrait), prevent upsize
            $img = Image::make($image)->fit(595, 842, function ($constraint) {
            $constraint->upsize();
            });

            Storage::disk('public')->put($path, (string) $img->encode('png'));

            $fields['image'] = '/storage/' . $path;
        }

        // Handle file upload
        if ($file = $request->file('file')) {
            $storedPath = $file->store('user-documents/files', 'public');
            $fields['file'] = '/storage/' . $storedPath;
        }

        // Save to database
        $userDocument = UserDocument::create($fields);

        return redirect()->route('userDocuments.show', $userDocument)
            ->with('success', 'User Document created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDocument $userDocument)
    {
        $user = User::find($userDocument->user_id);

        return inertia('UserDocuments/Show', [
            'userDocument' => new UserDocumentResource($userDocument),
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDocument $userDocument)
    {
        return inertia('UserDocuments/Edit', [
            'userDocument' => new UserDocumentResource($userDocument),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserDocumentUpdateRequest $request, UserDocument $userDocument)
    {
        $fields = $request->validated();

        // Handle image upload with Intervention Image
        if ($image = $request->file('image')) {
            // Delete old image if exists
            if ($userDocument->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $userDocument->image));
            }

            // Prepare filename based on user name if available, else use timestamp
            $user = isset($fields['user_id']) ? User::find($fields['user_id']) : null;
            $name = $user ? preg_replace('/\s+/', '', $user->name) : 'userdoc_' . time();
            $filename = 'userdoc_' . strtolower($name) . '.png';
            $path = 'user-documents/images/' . $filename;

            // Resize and fit image to 595x842 (A4 portrait), prevent upsize
            $img = Image::make($image)->fit(595, 842, function ($constraint) {
            $constraint->upsize();
            });

            Storage::disk('public')->put($path, (string) $img->encode('png'));

            $fields['image'] = '/storage/' . $path;
        }

        // Handle file upload
        if ($file = $request->file('file')) {
            // Delete old file if exists
            if ($userDocument->file) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $userDocument->file));
            }

            $storedPath = $file->store('user-documents/files', 'public');
            $fields['file'] = '/storage/' . $storedPath;
        }

        // Update the user document record
        $userDocument->update($fields);

        return redirect()->route('userDocuments.show', $userDocument)
            ->with('success', 'User Document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDocument $userDocument)
    {
        if ($userDocument->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $userDocument->image));
        }

        if ($userDocument->file) {
            Storage::disk('public')->delete($userDocument->file);
        }

        $userDocument->delete();

        return redirect(route('userDocuments.index'));
    }
}
