<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuffetRequest;
use App\Http\Requests\UpdateBuffetRequest;
use App\Models\Buffet;

class BuffetController extends Controller
{
    public function dashboard() {
        dd('Dashboard do buffet');
    }

    /**
     * Display the specified resource.
     */
    public function show(Buffet $buffet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buffet $buffet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBuffetRequest $request, Buffet $buffet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buffet $buffet)
    {
        //
    }
}
