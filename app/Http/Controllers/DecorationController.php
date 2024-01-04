<?php

namespace App\Http\Controllers;

use App\Models\Decoration;
use Illuminate\Http\Request;

class DecorationController extends Controller
{
    public function __construct(
        protected Decoration $decoration
    ){}
    public function index() {
        $decorations = $this->decoration->all();

        return view('decorations.index',compact('decorations'));
    }

    public function create() {
        
        return view('decorations.create');
    }
    public function store(Request $request) {
        
        return view('decorations.store');
    }
    public function show(){
        
        return view('decorations.show', compact('slug'));
    }
    public function update() {
        
        return view('decorations.update', compact('slug'));
    }
    public function edit (Request $request) {

        return view('decorations.edit', compact('id'));
    }
    public function delete(Request $request) {

        return view('decorations.delete', compact('id'));

    }
}
