<?php

namespace App\Http\Controllers;

use App\Http\Requests\Configuration\UpdateConfigurationRequest;
use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Models\Buffet;

class ConfigurationController extends Controller
{
    public function __construct(
        protected Buffet $buffet,
        protected Configuration $configuration,
        // protected Schedule $schedule,
        // protected Booking $booking,
        // protected Food $food,
        // protected Decoration $decoration,
        // protected Guest $guest,
        // protected Recommendation $recommendation
    )
    {
        // $this->hashids = new Hashids(config('app.name'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();

        // momentaneamente o index tem o mesmo conteudo do edit, dps faz uma tabelinha com as coisas

        return view("configurations.index", ['buffet' => $buffet,'configuration'=>$configuration]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();

        return view("configurations.index", ['buffet' => $buffet,'configuration'=>$configuration]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConfigurationRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = $this->buffet->where('slug', $buffet_slug)->first();
        
        if(!$buffet || !$buffet_slug) {
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }

        // adicionar autorizacao aqui
        $configuration = $this->configuration->where('buffet_id', $buffet->id)->first();


        if($request->buffet_whatsapp) {
            $configuration->buffet_whatsapp = $request->buffet_whatsapp;
        }

        $configuration->save();

        // $configuration->update([
        //     'content' => $request->content
        // ]);

        return redirect()->route('configurations.index', ['buffet'=>$buffet->slug])->with(['success'=>'Configuração atualizada com sucesso!']);

        dd($request);
    }
}
