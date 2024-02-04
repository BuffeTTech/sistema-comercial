<?php

namespace App\Http\Controllers;

use App\Http\Requests\Schedules\StoreScheduleRequest;
use App\Http\Requests\Schedules\UpdateScheduleRequest;
use App\Models\Buffet;
use App\Models\Schedule;
use App\Enums\ScheduleStatus;
use App\Enums\DayWeek;
use Hashids\Hashids;
use Illuminate\Http\Request; 

class ScheduleController extends Controller
{
    protected Hashids $hashids;
    
    public function __construct(
        protected Schedule $schedule,
        protected Buffet $buffet
    ) {
        $this->hashids = new Hashids(config('app.name'));
    }

    private function verify_conflict(Request $request, Schedule $schedule){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();
        $schedules = $this->schedule->where('buffet_id', $buffet->id)->where('status',ScheduleStatus::ACTIVE->name)->get();

        $isConflicted = false;

        $startDateTime = \Carbon\Carbon::parse($schedule->start_time);
        $endDateTime = $startDateTime->copy()->addMinutes($schedule->duration);

        foreach($schedules as $key => $value){
            $startValueTime = \Carbon\Carbon::parse($value->start_time);
            $endValueTime = $startDateTime->copy()->addMinutes($value->duration);

            if(((($startDateTime >= $startValueTime) && ($startDateTime <= $endValueTime)) && (($endDateTime <= $endValueTime) && ($endDateTime >= $startValueTime))) && ($schedule->day_week == $value->day_week)){
                $isConflicted = true;
            }

        }
        return $isConflicted;
    }
    
    private function verify_conflict_without_param(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();
        $schedules = $this->schedule->where('buffet_id', $buffet->id)->where('status',ScheduleStatus::ACTIVE->name)->get();

        $isConflicted = false;

        $startDateTime = \Carbon\Carbon::parse($request->start_time);
        $endDateTime = $startDateTime->copy()->addMinutes($request->duration);

        foreach($schedules as $key => $value){
            $startValueTime = \Carbon\Carbon::parse($value->start_time);
            $endValueTime = $startDateTime->copy()->addMinutes($value->duration);

            if(((($startDateTime >= $startValueTime) && ($startDateTime <= $endValueTime)) && (($endDateTime <= $endValueTime) && ($endDateTime >= $startValueTime))) && ($request->day_week == $value->day_week)){
                $isConflicted = true;
            }

        }
        return $isConflicted;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if (!$buffet || !$buffet_slug){
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        $schedules = $this->schedule->where('buffet_id', $buffet->id)
            ->orderByRaw("FIELD(day_week, '" . implode("', '", DayWeek::array()) . "')")
            ->orderBy('start_time', 'asc')
            ->paginate($request->get('per page', 5), ['*'], 'page', $request->get('page', 1)); 

        // $this->authorize('viewAny', [Schedule::class, $buffet]);
        
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
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        $this->authorize('create', [Schedule::class, $buffet]);
        
        return view('schedule.create', ['buffet'=>$buffet]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        if(!$buffet || !$buffet_slug){
            return redirect()->back()->withErrors(['buffet'=>'Buffet não encontrado'])->withInput();
        }
        $this->authorize('create', [Schedule::class, $buffet]);

        $
        $isConflicted = $this->verify_conflict_without_param($request);
        if($isConflicted){
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


        return redirect()->route('schedule.show', ['buffet'=>$buffet_slug, 'schedule' =>$schedule->hashed_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();

        $schedule_id = $this->hashids->decode($request->schedule)[0];

        if(!$schedule = $this->schedule->where('id', $schedule_id)->where('buffet_id', $buffet->id)->first()){
            return redirect()->route('schedule.index', ['buffet'=>$buffet_slug])->withErrors(['schedule'=>'schedule not found'])->withInput();
        }

        $this->authorize('view', [Schedule::class, $schedule, $buffet]);

        
        return view('schedule.show',['buffet'=>$buffet_slug, 'schedule'=>$schedule]); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $schedule_id = $this->hashids->decode($request->schedule)[0];

        $schedule = Schedule::where('id', $schedule_id)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule'=>'schedule not found'])->withInput();
        }
        $this->authorize('update', [Schedule::class, $schedule, $buffet]);

        return view('schedule.update', ['buffet'=>$buffet_slug, 'schedule'=>$schedule]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $schedule_id = $this->hashids->decode($request->schedule)[0];
        
        $schedule = Schedule::where('id', $schedule_id)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule'=>'schedule not found'])->withInput();
        }
        $this->authorize('update', [Schedule::class, $schedule, $buffet]);

        $isConflicted = $this->verify_conflict($request, $schedule);
        if($isConflicted){
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
        
        $schedule_id = $this->hashids->decode($request->schedule)[0];

        $schedule = Schedule::where('id', $schedule_id)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule'=>'schedule not found'])->withInput();
        }
        $this->authorize('destroy', [Schedule::class, $schedule, $buffet]);

        $schedule->update(['status'=> ScheduleStatus::UNACTIVE->name]);


        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug]);
    }

    public function change_status(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();
        $schedule_id = $this->hashids->decode($request->schedule)[0];
        
        $schedule = $this->schedule->where('id', $schedule_id)->where('buffet_id', $buffet->id)->first();
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule' => 'schedule not found'])->withInput();
        }
        $this->authorize('change_status', [Schedule::class, $schedule, $buffet]);

        $isConflicted = $this->verify_conflict($request,$schedule);
        if($isConflicted){
            return redirect()->back()->withErrors(['start_time' => 'Schedule conflicts with existing schedules for the selected day of the week'])->withInput();
        }
        
        $schedule->update(['status'=>$request->status]); 

        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug]); 
    }
}
