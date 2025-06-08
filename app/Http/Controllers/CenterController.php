<?php

namespace App\Http\Controllers;

use App\Http\Requests\CenterStoreRequest;
use App\Http\Requests\CenterUpdateRequest;
use App\Http\Resources\CenterResource;
use App\Http\Resources\CoordinatorResource;
use App\Http\Resources\UserResource;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function index(Request $request)
    {
        $query = Center::with('coordinator');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('coordinator', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $centers = CenterResource::collection(
            $query->with(['coordinator.user', 'students'])->paginate(15)->withQueryString()
        );

        return inertia('Centers/Index', [
            'centers' => $centers,
        ]);
    }

    public function show(Center $center)
    {
        $center = new CenterResource($center->load('students'));

        if($center->coordinator){
            $coordinator = new CoordinatorResource($center->coordinator->load('user'));
        }else{
            $coordinator = null;
        }       
        
        return inertia('Centers/Show', [
            'center' => $center,
            'coordinator' => $coordinator,
        ]);
    }

    public function create()
    {
        return inertia('Centers/Create', [
            'users' => UserResource::collection(User::all()),
        ]);
    }

    public function store(CenterStoreRequest $request)
    {
        $fields = $request->validated();
        // Generate Center Code
        $counCenters = Center::count();

        $fields['code'] = 'SITS-C-'.str_pad($counCenters + 1, 3, '0', STR_PAD_LEFT);

        $center = Center::updateOrCreate($fields);
        
        return redirect()->route('centers.show', $center)->with('success', 'Center created successfully.');
    }

    public function edit(Center $center)
    {
        return inertia('Centers/Edit', [
            'center' => new CenterResource($center),
            'users' => UserResource::collection(User::all()),
        ]);
    }

    public function update(CenterUpdateRequest $request, Center $center)
    {
        $fields = $request->validated();

        $center->update($fields);
        
        return redirect()->route('centers.show', $center)->with('success', 'Center updated successfully.');
    }

    public function destroy(Center $center)
    {
        $center->delete();

        // Delete the coordinator and the related user
        if ($center->coordinator) {
            $coordinator = $center->coordinator;
            if ($coordinator->user) {
            $coordinator->user->delete();
            }
            $coordinator->delete();
        }

        return redirect()->route('centers.index')->with('success', 'Center deleted successfully.');
    }
}
