<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDocumentStoreRequest;
use App\Http\Requests\UserDocumentUpdateRequest;
use App\Http\Resources\UserDocumentResource;
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
    public function create()
    {
        return inertia('UserDocuments/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserDocumentStoreRequest $request)
    {
        $fields = $request->validated();

        // Handle image upload with Intervention
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image');
            $image = Image::make($imagePath); // Intervention Image instance
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio(); // Preserve aspect ratio
                $constraint->upsize(); // Prevent upsizing
            });

            $imagePath = 'user-documents/images/' . time() . '-' . $imagePath->getClientOriginalName();
            $image->save(storage_path('app/public/' . $imagePath));

            $fields['image'] = '/storage/' . $imagePath;
        }


        // Handle file upload
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('user-documents/files', 'public');
            $fields['file'] = '/storage/'.$filePath;
        }

        $userDocument = UserDocument::create($fields);

        return redirect(route('userDocuments.show', $userDocument))->with('success', 'User Document created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDocument $userDocument)
    {
        return inertia('UserDocuments/Show', [
            'userDocument' => new UserDocumentResource($userDocument),
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

        // Handle image upload with Intervention
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($userDocument->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $userDocument->image));
            }

            $imagePath = $request->file('image');
            $image = Image::make($imagePath); // Intervention Image instance
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio(); // Preserve aspect ratio
                $constraint->upsize(); // Prevent upsizing
            });

            $imagePath = 'user-documents/images/' . time() . '-' . $imagePath->getClientOriginalName();
            $image->save(storage_path('app/public/' . $imagePath));

            $fields['image'] = '/storage/' . $imagePath;
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            if ($userDocument->file) {
                Storage::disk('public')->delete($userDocument->file); // Delete old file
            }
            $filePath = $request->file('file')->store('user-documents/files', 'public');
            $fields['file'] = '/storage/'.$filePath;
        }

        // Update the user document record
        $userDocument->update($fields);

        return redirect(route('userDocuments.show', $userDocument))->with('success', 'User Document updated successfully.');
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
