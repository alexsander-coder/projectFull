<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Link;
use Illuminate\Support\Str;
use App\Models\LinkAccess;
use Illuminate\Validation\Rule;

class LinkController extends Controller
{
    public function index()
    {
    $baseUrl = 'http://localhost:8000/api';
    $links = Link::orderBy('created_at', 'desc')->get();

    $linksWithFullShortLink = $links->map(function ($link) use ($baseUrl) {
        return [
            'id' => $link->id,
            'original_link' => $link->original_link,
            'short_link' => $link->short_link,
            'clicks' => $link->clicks,
            'nome_ficticio' => $link->nome_ficticio,
            'created_at' => $link->created_at,
            'updated_at' => $link->updated_at,
        ];
    });

    return response()->json(['data' => $linksWithFullShortLink], 200);
    }



    public function store(Request $request)
    {
        $rules = [
            'original_link' => 'required|url',
            'short_link' => 'nullable|alpha_num|between:6,8|unique:links,short_link',
            'nome_ficticio' => 'nullable',
        ];

        $messages = [
            'short_link.between' => 'O identificador único deve ter entre 6 e 8 caracteres.',
            'short_link.unique' => 'Identificador único já em uso'
        ];

        $data = $request->validate($rules, $messages);
        
        $shortLink = empty($data['short_link']) ? Str::random(6) : $data['short_link'];


        $link = Link::create([
            'original_link' => $data['original_link'],
            'short_link' => $shortLink,
            'nome_ficticio' => $data['nome_ficticio']
        ]);

        
        return response()->json(['message' => 'Link criado com sucesso!', 'data' => $link], 201);
    }
    

    public function show(Link $link)
    {
    $baseUrl = 'http://localhost:8000/api';
    
    $linkAccesses = $link->linkAccesses;

    $linkData = [
        'id' => $link->id,
        'original_link' => $link->original_link,
        'short_link' => $baseUrl . '/' . $link->short_link,
        'clicks' => $link->clicks,
        'nome_ficticio' => $link->nome_ficticio,
        'created_at' => $link->created_at,
        'updated_at' => $link->updated_at,
    ];

    return response()->json([
        'link' => $linkData,
        'accesses' => $linkAccesses
    ]);
}


    public function redirect($shortLink)
    {
        $link = Link::where('short_link', $shortLink)->firstOrFail();
        
        $ip = request()->ip();
        $userAgent = request()->header('User-Agent');

        LinkAccess::create([
            'link_id' => $link->id,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);

        $link->increment('clicks');

        return redirect($link->original_link);
    }

    
    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);
        
        $rules = [
            'original_link' => 'url',
            'short_link' => [
                'alpha_num',
                'between:6,8',
                Rule::unique('links')->ignore($link->id)
            ],
            'nome_ficticio' => 'nullable',
        ];
        
        $data = $request->validate($rules);

        $link->update($data);
        
        return response()->json(['message' => 'Link atualizado com sucesso!', 'data' => $link], 201);

    }

    public function destroy($id)  
     {
         try {
             $link = Link::findOrFail($id);
             $link->delete();
 
             return response()->json(['message' => 'Link deletado com sucesso!'], 200);  
         } catch (ModelNotFoundException $e) {
             return response()->json(['error' => 'Link não encontrado!'], 404);
         }
     }
}