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
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if (!$buffet || !$buffet_slug){
            return null; 
        }
        $schedules = $this->schedule->where('buffet_id', $buffet->id)
        ->orderByRaw("FIELD(day_week, '" . implode("', '", DayWeek::array()) . "')")
        ->orderBy('start_time', 'asc')
        ->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1)); 
        
        return view('schedule.index', ['buffet'=>$buffet_slug, 'schedules'=>$schedules]); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug){
            return null; 
        }
        
        return view('schedule.create', ['buffet'=>$buffet_slug]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug){
            return null; 
        }

        $startDateTime = \Carbon\Carbon::parse($request->start_time);
        $endDateTime = $startDateTime->copy()->addMinutes($request->duration);

        // Verifica conflitos de horário e dia da semana
        $conflictingSchedules = $this->schedule
            ->where('buffet_id', $buffet->id)
            ->where('day_week', $request->day_week)
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where('start_time', '>=', $startDateTime)
                        ->where('start_time', '<', $endDateTime);
                })
                ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where('start_time', '<', $startDateTime)
                        ->where('start_time', '>', $endDateTime);
                });
            })
            ->get();

        if (count($conflictingSchedules) !== 0) {
            return redirect()->back()->withErrors(['start_time' => 'Schedule conflicts with existing schedules for the selected day of the week'])->withInput();
        }

        $schedule = $this->schedule->create([
            'day_week' => $request->day_week,
            'start_time' => $request->start_time, 
            'duration' => $request->duration, 
            'start_block'=> $request->start_block,
            'end_block' => $request->end_block, 
            'status' => $request->status ?? ScheduleStatus::ACTIVE->name, 
            'buffet_id'=> $buffet->id, 
        ]); 

        return redirect()->route('schedule.show', ['buffet'=>$buffet_slug, 'schedule' =>$schedule]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if(!$schedule = $this->schedule->where('id', $request->schedule)->where('buffet_id', $buffet->id)->first()){
            return redirect()->route('schedule.index', ['buffet'=>$buffet_slug])->withErrors(['schedule'=>'schedule not found'])->withInput();
        }
        
        return view('schedule.show',['buffet'=>$buffet_slug, 'schedule'=>$schedule]); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();
        $schedule = Schedule::where('id', $request->schedule)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule'=>'schedule not found'])->withInput();
        }

        return view('schedule.update', ['buffet'=>$buffet_slug, 'schedule'=>$schedule]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $schedule = Schedule::where('id', $request->schedule)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule'=>'schedule not found'])->withInput();
        }

        $schedule_exists = $this->schedule->where('id', $request->schedule)->where('buffet_id', $buffet->id)->get()->first();
        // if($schedule_exists && $schedule_exists->id !== $schedule->id) {
        //     return redirect()->back()->withErrors(['slug' => 'schedule already exists.'])->withInput();
        // }

        $startDateTime = \Carbon\Carbon::parse($request->start_time);
        $endDateTime = $startDateTime->copy()->addMinutes($request->duration);

        // Verifica conflitos de horário e dia da semana
        $conflictingSchedules = $this->schedule
            ->where('buffet_id', $buffet->id)
            ->where('day_week', $request->day_week)
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where('start_time', '>=', $startDateTime)
                        ->where('start_time', '<', $endDateTime);
                })
                ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                    $q->where('start_time', '<', $startDateTime)
                        ->where('start_time', '>', $endDateTime);
                });
            })
            ->get();

        if (count($conflictingSchedules) > 1 || ($schedule_exists->id !== $schedule->id && count($conflictingSchedules) === 1)) {
            return redirect()->back()->withErrors(['start_time' => 'Schedule conflicts with existing schedules for the selected day of the week'])->withInput();
        }

        $schedule->update([
            'day_week' => $request->day_week,
            'start_time' => $request->start_time, 
            'duration' => $request->duration, 
            'start_block'=> $request->start_block,
            'end_block' => $request->end_block, 
            'status' => $request->status ?? ScheduleStatus::ACTIVE->name, 
            'buffet_id'=> $buffet->id, 
        ]); 

        return redirect()->route('schedule.show', ['buffet'=>$buffet_slug, 'schedule'=>$schedule->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first(); 

        $schedule = Schedule::where('id', $request->schedule)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule'=>'schedule not found'])->withInput();
        }

        $schedule->update(['status'=> ScheduleStatus::UNACTIVE->name]);

        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug]);
    }

    public function change_status(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();
        
        $schedule = $this->schedule->where('id', $request->schedule)->where('buffet_id', $buffet->id)->first();
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule' => 'schedule not found'])->withInput();
        }

        $schedule->update(['status'=>$request->status]); 

        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug]); 
    }
}
