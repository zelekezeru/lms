<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicDocumentRequest;
use App\Models\AcademicDocument;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicDocumentController extends Controller
{
    public function index(): Response
    {
        $documents = AcademicDocument::with('user')->latest()->get();
        return Inertia::render('AcademicDocuments/Index', ['documents' => $documents]);
    }

    public function create(): Response
    {
        return Inertia::render('AcademicDocuments/Form');
    }

    public function store(AcademicDocumentRequest $request)
    {
        AcademicDocument::create($request->validated());
        return redirect()->route('academic-documents.index')->with('success', 'Document added successfully.');
    }

    public function edit(AcademicDocument $academicDocument): Response
    {
        return Inertia::render('AcademicDocuments/Form', ['document' => $academicDocument]);
    }

    public function update(AcademicDocumentRequest $request, AcademicDocument $academicDocument)
    {
        $academicDocument->update($request->validated());
        return redirect()->route('academic-documents.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(AcademicDocument $academicDocument)
    {
        $academicDocument->delete();
        return redirect()->route('academic-documents.index')->with('success', 'Document deleted successfully.');
    }
}
