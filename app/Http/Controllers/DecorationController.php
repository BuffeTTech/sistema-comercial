<?php

namespace App\Http\Controllers;

use App\Enums\DecorationStatus;
use App\Http\Requests\Decorations\StoreDecorationRequest;
use App\Http\Requests\Decorations\UpdateDecorationRequest;
use App\Models\Buffet;
use App\Models\Decoration;
use App\Models\DecorationPhotos;
use Illuminate\Http\Request;

class DecorationController extends Controller
{
    public function __construct(
        protected Decoration $decoration,
        protected Buffet $buffet,
        protected DecorationPhotos $photos,
    )
    {
    }

    public static string $image_repository = '/app/public/decorations';

    private function upload_image($photo) {
        if ($photo->isValid()) {
            $file_name = $photo->getClientOriginalName();
            $file_extension = $photo->getClientOriginalExtension();
            $file_size = $photo->getSize();
            $mime_type = $photo->getMimeType();
            
            $imageName = sanitize_string(explode($file_extension, $file_name)[0]).time() . rand(1, 99) . '-.' . $file_extension;
            $file_path = "/".$imageName;

            // $foto->move(public_path('uploads'), $file_path);
            $photo->move(storage_path(self::$image_repository), $imageName);

            return [
                "file_name"=>$file_name,
                "file_extension"=>$file_extension,
                "file_size"=>$file_size,
                "mime_type"=>$mime_type,
                "file_path"=>$file_path,
            ];
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return null;
        }

        $decorations = $this->decoration->where('buffet',$buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
        return view('decoration.index',['decorations'=>$decorations,'buffet'=>$buffet_slug],);
    }

    public function not_found() {
        return view('decoration.decoration-not-found');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return null;
        }

        return view('decoration.create', ['buffet'=>$buffet]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDecorationRequest $request)
    {
        $slug = str_replace(' ', '-', $request->slug);
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        //dd($buffet); 
        if($this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['slug' => 'decoration already exists.'])->withInput();
        }

        $decoration = $this->decoration->create([
            'main_theme'=>$request->main_theme,
            'slug'=>$slug,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=> $request->status ?? DecorationStatus::ACTIVE->name,
            'buffet'=> $buffet->id
        ]);

        if($request->has('decoration_photos')){
            $decoration_photos = $request->decoration_photos;
            foreach($decoration_photos as $photo){
                if($photo->isValid()){
                    if($upload = $this->upload_image(photo: $photo)){
                        $this->photos->create([
                            'file_name'=>$upload['file_name'],
                            'file_path'=>$upload['file_path'],
                            'file_extension'=>$upload['file_extension'],
                            'mime_type'=>$upload['mime_type'],
                            'file_size'=>$upload['file_size'],
                            'decorations'=>$decoration->id,
                        ]);
                    } else{
                        return redirect()->route('decoration.show', ['buffet'=>$request->buffet, 'decoration'=>$request->decoration])->withErrors(['photo'=>"error photo not valid"])->withInput();
                    }
                }
            }
        }
        return redirect()->route('decoration.show',['buffet'=>$buffet_slug, 'decoration'=>$decoration]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if(!$decoration= $this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->route('decoration.index', $buffet_slug)->withErrors(['slug' => 'decoration not found.'])->withInput();
        }

        //$decoration = $this->decoration->where('slug',$request->decoration)->get()->first();
        $decoration_slug = $request->decoration; 
        $decoration_photos = DecorationPhotos::where('decorations', $decoration_slug)->get(); 

        return view('decoration.show',['buffet'=>$buffet_slug, 'decoration'=>$decoration, 'decoration_photos'=>$decoration_photos]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if(!$decoration= $this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->route('decoration.index', $buffet_slug)->withErrors(['slug' => 'deoration not found.'])->withInput();
        }

        $decoration_slug = $request->decoration; 
        $decoration_photos = DecorationPhotos::where('decorations', $decoration_slug)->get(); 

        return view('decoration.update',['buffet'=>$buffet,'decoration'=>$decoration, 'decoration_photos'=>$decoration_photos]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDecorationRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug',$buffet_slug)->get()->first();

        $decoration = $this->decoration->where('slug',$request->decoration)->where('buffet', $buffet->id)->get()->first();
        if(!$decoration){
            return redirect()->back()->whithErrors('slug', 'decoration not found')->withInput; 
        }

        $decoration_exists = $this->decoration->where('slug', $request->slug)->where('buffet', $buffet->id)->get()->first();
        if($decoration_exists && $decoration_exists->id !== $decoration->id){
            return redirect()->back()->withErrors(['slug' => 'decoration already exists'])->withInput(); 
        }

        $decoration->update([
            'main_theme' => $request->main_theme,
            'slug'=>$request->slug,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=> $request->status ?? DecorationStatus::ACTIVE->name,
            'buffet'=> $buffet->id
        ]);

        $dec = $this->decoration->find($decoration->id);
        
        $decoration_photos = $this->photos->where('slug', $request->slug)->where('decorations', $decoration->id)->get(); 

        return redirect()->back(); // para ser possivel update foto e conteudos ao mesmo tempo 

    }

    public function update_photo(Request $request){
        $decoration_slug = $request->decoration; 
        $decoration = Decoration::where('slug', $decoration_slug)->first();

        $decoration_photos_slug = $request->decoration_photos; 
        $decoration_photos = $this->photos->where('id', $decoration_photos_slug)->where('decorations', $decoration->id)->get()->first();

        if(!$decoration_photos){
            return redirect()->route('decoration.index', ['buffet'=>$request->buffet])->withErrors(['photo'=>"photo not found"])->withInput();
        }

        $previous_file_path = storage_path(self::$image_repository).$decoration_photos->file_path; 
        $photo_id = $this->photos->find($decoration_photos->id);

        $photo = $request->photos; 
        if($request->has('photo')){
            if ($photo->isValid()){
                if($upload = $this->upload_image(photo: $photo)){
                    if(file_exists($previous_file_path)){ // deletar a foto anterior para poupar armazenamento 
                        unlink($previous_file_path);
                    }
                    $decoration_photos->update([
                        'file_name'=>$upload['file_name'],
                        'file_path'=>$upload['file_path'],
                        'file_extension'=>$upload['file_extension'],
                        'mime_type'=>$upload['mime_type'],
                        'file_size'=>$upload['file_size'],
                        'decorations'=>$decoration->id,
                    ]);
                } else {
                    return redirect()->back()->withErrors(['photo'=>"error photo not valid"])->withInput();
                }
            }
        }
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */

    public function change_status(Request $request)
     {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $decoration = $this->decoration->where('slug', $request->decoration)->where('buffet', $buffet->id)->get()->first();
        if (!$decoration) {
            return redirect()->back()->withErrors(['slug' => 'decoration not found.'])->withInput();
        }

        $decoration->update(['status'=>$request->status]);

        return redirect()->route('decoration.index', ['buffet'=>$buffet_slug]);
     }
}
