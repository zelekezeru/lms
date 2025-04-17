<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResultStoreRequest;
use App\Http\Requests\ResultUpdateRequest;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $results = Result::with(['student', 'year', 'semester', 'section', 'course'])->paginate(30);

        return inertia('Results/Index', [
            'results' => $results,
        ]);
    }
    
    public function create()
    {
        return inertia('Results/Create');
    }

    public function store(ResultStoreRequest $request)
    {        
        $fields = $request->validated();

        Result::create($fields);

        return redirect()->route('results.index')->with('success', 'Result created successfully.');
    }

    public function show(Result $result)
    {
        return inertia('Results/Show', [
            'result' => $result,
        ]);
    }

    public function edit(Result $result)
    {
        return inertia('Results/Edit', [
            'result' => $result,
        ]);
    }

    public function update(ResultUpdateRequest $request, Result $result)
    {
        $fields = $request->validated();

        $result->update($fields);

        return redirect()->route('results.index')->with('success', 'Result updated successfully.');
    }

    public function destroy(Result $result)
    {
        $result->delete();

        return redirect()->route('results.index')->with('success', 'Result deleted successfully.');
    }
}
