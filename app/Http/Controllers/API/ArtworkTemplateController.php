<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtworkTemplateCollection;
use App\Http\Resources\ArtworkTemplateResource;
use App\Models\ArtworkTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;

class ArtworkTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:generate artworks'], ['only' => ['generate']]);
        $this->middleware(['permission:view artwork templates|create artwork templates|edit artwork templates|delete artwork templates'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:view artwork templates'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:create artwork templates'], ['only' => ['store']]);
        $this->middleware(['permission:edit artwork templates'], ['only' => ['update']]);
        $this->middleware(['permission:delete artwork templates'], ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $artworks = ArtworkTemplate::all();

        return new ArtworkTemplateCollection($artworks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:250|unique:artwork_templates,title',
            'photo' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('artworks', 'public');
        }

        $artwork = ArtworkTemplate::create($data);

        return (new ArtworkTemplateResource($artwork))->response()->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ArtworkTemplate $artwork): ArtworkTemplateResource
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:250', Rule::unique('artwork_templates', 'title')->ignore($artwork->id)],
            'photo' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            if($artwork->photo && Storage::disk('public')->exists($artwork->photo)) {
                Storage::disk('public')->delete($artwork->photo);
            }
            $data['photo'] = $request->file('photo')->store('artworks', 'public');
        } else {
            $data['photo'] = $artwork->photo;
        }

        $artwork->update($data);

        return new ArtworkTemplateResource($artwork);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArtworkTemplate $artwork): ArtworkTemplateResource
    {
        if ($artwork->photo && Storage::disk('public')->exists($artwork->photo)) {
            Storage::disk('public')->delete($artwork->photo);
        }

        $artwork->delete();

        return new ArtworkTemplateResource($artwork);
    }

    public function generate()
    {
        $artworks = ArtworkTemplate::select(['id', 'photo'])->inRandomOrder()->take(10)->get();

        $artwork = null;
        foreach ($artworks as $key => $value) {
            if (Storage::disk('public')->exists($value->photo)) {
                $artwork = ArtworkTemplate::find($value->id);
                break;
            }
        }

        $imageData = Storage::disk('public')->get($artwork->photo);
        $img = Image::read($imageData)->toJpg()->toDataUri();

        return response()->json([
            'data' => $img
        ]);
    }
}
