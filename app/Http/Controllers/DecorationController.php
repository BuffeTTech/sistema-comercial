<?php

namespace App\Http\Controllers;

use App\Enums\DecorationStatus;
use App\Http\Requests\Decorations\StoreDecorationRequest;
use App\Http\Requests\Decorations\UpdateDecorationRequest;
use App\Models\Buffet;
use App\Models\BuffetSubscription;
use App\Models\Decoration;
use App\Models\DecorationPhotos;
use App\Models\SubscriptionConfiguration;
use Carbon\Carbon;
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
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();        
        }
        $this->authorize('viewAny', [Decoration::class, $buffet]);

        $decorations = $this->decoration->where('buffet_id',$buffet->id)->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1));
        return view('decoration.index',['decorations'=>$decorations,'buffet'=>$buffet],);
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
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        $this->authorize('create', [Decoration::class, $buffet]);

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        return view('decoration.create', ['buffet'=>$buffet, 'configurations'=>$configurations]);
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
        if($decoration = $this->decoration->where('slug', $slug)->where('buffet_id', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['slug' => 'decoration already exists.'])->withInput();
        }

        $this->authorize('create', [Decoration::class, $buffet]);

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }
        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();
        if((!isset($request->decoration_photos) || count($request->decoration_photos) != $configurations['max_decoration_photos']) && $configurations['max_decoration_photos'] !== null) {
            return redirect()->back()->withErrors(['photo'=> 'Não existem '.$configurations['max_decoration_photos'].' fotos na requisição'])->withInput();
        }

        $decoration = $this->decoration->create([
            'main_theme'=>$request->main_theme,
            'slug'=>$slug,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=> $request->status ?? DecorationStatus::ACTIVE->name,
            'buffet_id'=> $buffet->id
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
                            'decorations_id'=>$decoration->id,
                        ]);
                    }
                }
            }
        }
        return redirect()->back()->with(['success'=>'Decoração criada com sucesso!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if(!$decoration= $this->decoration->where('slug', $request->decoration)->where('buffet_id', $buffet->id)->get()->first()){
            return redirect()->route('decoration.index', $buffet_slug)->withErrors(['slug' => 'decoration not found.'])->withInput();
        }

        $this->authorize('view', [Decoration::class, $decoration, $buffet]);

        //$decoration = $this->decoration->where('slug',$request->decoration)->get()->first();
        $decoration_slug = $request->decoration; 
        $decoration_photos = DecorationPhotos::where('decorations_id', $decoration->id)->get(); 
        

        return view('decoration.show',['buffet'=>$buffet, 'decoration'=>$decoration, 'decoration_photos'=>$decoration_photos]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        if(!$decoration= $this->decoration->where('slug', $request->decoration)->where('buffet_id', $buffet->id)->get()->first()){
            return redirect()->route('decoration.index', $buffet_slug)->withErrors(['slug' => 'deoration not found.'])->withInput();
        }
        $this->authorize('update', [Decoration::class, $decoration, $buffet]);

        $buffet_subscription = BuffetSubscription::where('buffet_id', $buffet->id)->with('subscription')->latest()->first();
        if($buffet_subscription->expires_in < Carbon::now()) {
            return redirect()->back()->withErrors(['buffet'=> "Buffet is not active"])->withInput();
        }

        $configurations = SubscriptionConfiguration::where('subscription_id', $buffet_subscription->subscription_id)->get()->first();

        $decoration_slug = $request->decoration; 
        $decoration_photos = DecorationPhotos::where('decorations_id', $decoration->id)->get(); 

        return view('decoration.update',['buffet'=>$buffet,'decoration'=>$decoration, 'decoration_photos'=>$decoration_photos, 'configurations'=>$configurations]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDecorationRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $decoration = $this->decoration->where('slug',$request->decoration)->where('buffet_id', $buffet->id)->get()->first();
        if(!$decoration){
            return redirect()->back()->whithErrors('slug', 'decoration not found')->withInput; 
        }
        $this->authorize('update', [Decoration::class, $decoration, $buffet]);
        
        $decoration_exists = $this->decoration->where('slug', $request->slug)->where('buffet_id', $buffet->id)->get()->first();
        if($decoration_exists && $decoration_exists->id !== $decoration->id){
            return redirect()->back()->withErrors(['slug' => 'decoration already exists'])->withInput(); 
        }

        $decoration->update([
            'main_theme' => $request->main_theme,
            'slug'=>$request->slug,
            'description'=>$request->description,
            'price'=>$request->price,
            'status'=> $request->status ?? DecorationStatus::ACTIVE->name,
            'buffet_id'=> $buffet->id
        ]);

        $dec = $this->decoration->find($decoration->id);
        
        $decoration_photos = $this->photos->where('id', $request->slug)->where('decorations_id', $decoration->id)->get(); 

        return redirect()->route('decoration.edit', ['buffet'=>$buffet->slug, 'decoration'=>$dec->slug])->with(['success'=>'Decoração atualizada!']);

    }

    public function update_photo(Request $request){
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $decoration = $this->decoration->where('slug',$request->decoration)->where('buffet_id', $buffet->id)->get()->first();
        if(!$decoration){
            return redirect()->back()->whithErrors('slug', 'decoration not found')->withInput; 
        }
        $decoration_photos = $this->photos->where('id', $request->decoration_photos)->where('decorations_id', $decoration->id)->get()->first();
        if(!$decoration_photos){
            return redirect()->route('decoration.index', ['buffet'=>$buffet->slug])->withErrors(['photo'=>"photo not found"])->withInput();
        }
        $this->authorize('update', [Decoration::class, $decoration, $buffet]);

        $previous_file_path = storage_path(self::$image_repository).$decoration_photos->file_path; 
        $photo_id = $this->photos->find($decoration_photos->id);

        $photo = $request->photo; 
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
                        'decorations_id'=>$decoration->id,
                    ]);
                }
            }
        }
        return redirect()->route('decoration.edit', ['buffet'=>$buffet->slug, 'decoration'=>$decoration->slug]);
    }

    public function destroy(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        if (!$decoration = $this->decoration->where('slug', $request->decoration)->where('buffet_id', $buffet->id)->get()->first()) {
           return redirect()->back()->withErrors(['slug' => 'Decoração não encontrada.'])->withInput();
        }
        $this->authorize('change_status', [Decoration::class, $decoration, $buffet]);

        $decoration->update(['status' => DecorationStatus::UNACTIVE->name]);

        return redirect()->back()->with(['success'=>'Decoração deletada com sucesso.'])->withInput();
    }

    public function activate_decoration(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        if (!$decoration = $this->decoration->where('slug', $request->decoration)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'decoration not found.'])->withInput();
        }
        $this->authorize('change_status', [Decoration::class, $decoration, $buffet]);
        
        $decoration->update(['status' => DecorationStatus::ACTIVE->name]);
        
        return redirect()->back()->with(['success'=>'Decoração ativada com sucesso.'])->withInput();
     }


    /**
     * Remove the specified resource from storage.
     */

    public function change_status(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        
        if (!$decoration = $this->decoration->where('slug', $request->decoration)->where('buffet_id', $buffet->id)->get()->first()) {
            return redirect()->back()->withErrors(['slug' => 'decoration not found.'])->withInput();
        }
        $this->authorize('change_status', [Decoration::class, $decoration, $buffet]);
        $decoration->update(['status'=>$request->status]);

        return redirect()->back()->with(['success'=>'Status da decoração atualizado com sucesso!']);
    }

    // API
    public function api_get_decoration(Request $request) {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();

        if(!$buffet || !$buffet_slug) {
            return response()->json(['message' => 'Buffet not found'], 422);
        }
        
        if(!$decoration = $this->decoration->where('slug', $request->decoration)->where('buffet_id', $buffet->id)->with('photos')->get()->first()){
            return response()->json(['message' => 'Decoration not found'], 422);;
        }

        return response()->json(['data'=>$decoration], 200);;
    }
}
