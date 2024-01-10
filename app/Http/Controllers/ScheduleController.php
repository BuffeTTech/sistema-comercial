<?php

namespace App\Http\Controllers;

use App\Http\Requests\Schedules\StoreScheduleRequest;
use App\Http\Requests\Schedules\UpdateScheduleRequest;
use App\Models\Buffet;
use App\Models\Schedule;
use App\Enums\ScheduleStatus;
use App\Enums\DayWeek;
use Illuminate\Http\Request; 

class ScheduleController extends Controller
{
    public function __construct(
        protected Schedule $schedule,
        protected Buffet $buffet
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug);

        if (!$buffet || $buffet_slug){
            return null; 
        }
        $schedules = $this->schedule->where('buffet', $buffet->id)->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1)); 
        return view('schedules.index', ['buffet'=>$buffet_slug, 'schedules'=>$schedules]); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug); 

        if(!$buffet || $buffet_slug){
            return null; 
        }

        return view('schedules.create', ['buffet'=>$buffet_slug]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        $slug = str_replace(' ', '-', $request->slug);

        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug); 

        if($this->schedule->where('id', $request->schedule)->where('buffet', $buffet->id)->get()->first()){
            return redirect()->back()->withErrors(['schedule' => 'schedule already exists'])->withInput();
        }

        $schedule = $this->schedule->create([
            'day_week' => $request->day_week,
            'start_time' => $request->start_time, 
            'duration' => $request->time, 
            'start_block'=> $request->start_block,
            'end_block' => $request->end_block, 
            'status' => $request->status ?? ScheduleStatus::ACTIVE->name, 
            'buffet'=> $buffet->id, 
        ]); 

        return redirect()->route('schedule.show', ['buffet'=>$buffet_slug, 'schedule' =>$schedule]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug); 

        if(!$schedule = $this->schedule->where('id', $request->schedule)->where('buffet', $buffet->id)){
            return redirect()->route('schedule.index', ['buffet'=>$buffet_slug])->withErrors(['schedule'=>'schedule not found'])->withInput();
        }

        return view('schedule.show',['buffet'=>$buffet_slug, 'schedule'=>$schedule]); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
