<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkCollection;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|url',
        ]);

        $link_collection = LinkCollection::find(request('link_collection_id'));

        $link = $link_collection->links()->create([
            'url' => request('url'),
            'descripcion' => request('descripcion'),
            'link_collection_id' => request('link_collection_id'),
        ]);

        $link->orden = $link->id;
        $link->save();

        return back()->with('success', 'Link Successfully Saved');
    }

    public function destroy(Link $link)
    {
        $link->delete();

        return back();
    }
}
