<?php

namespace App\Http\Controllers;

use App\DTO\Schedules\VerifyConflictDTO;
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
    
    private function verify_conflict(VerifyConflictDTO $dto, Buffet $buffet){
        $buffet_slug = $buffet->slug; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();
        $schedules = $this->schedule->where('day_week', $dto->day_week->name)->where('buffet_id', $buffet->id)->where('status',ScheduleStatus::ACTIVE->name)->get();

        $isConflicted = false;

        $startDateTime = \Carbon\Carbon::parse($dto->start_time);
        $endDateTime = $startDateTime->copy()->addMinutes($dto->duration);
        $dayWeek = $dto->day_week->name;

        foreach($schedules as $schedule_loop){
            if(isset($dto->id) && $dto->id == $schedule_loop->id){
                continue;
            }
            
            $startDbTime = \Carbon\Carbon::parse($schedule_loop->start_time);
            $endDbTime = $startDbTime->copy()->addMinutes($schedule_loop->duration);
            
            $validateIfNextStartTimeIsLaterThanStart = $startDateTime >= $startDbTime;
            $validateIfNextStartTimeIsEarlierThanEnd = $startDateTime <= $endDbTime;
            $validateIfNextStartTimeIsBetweenAnotherSchedule = $validateIfNextStartTimeIsLaterThanStart && $validateIfNextStartTimeIsEarlierThanEnd;
            
            $validateIfNextEndTimeIsEarlierThanEnd = $endDateTime <= $endDbTime;
            $validateIfNextEndTimeIsLaterThanStart = $endDateTime >= $startDbTime;
            $validateIfNextEndTimeIsBetweenAnotherSchedule = $validateIfNextEndTimeIsEarlierThanEnd && $validateIfNextEndTimeIsLaterThanStart;
            
            $validateIfNextScheduleIsOutRange = $startDbTime >= $startDateTime && $endDbTime <= $endDateTime;

            if(
                ((($validateIfNextStartTimeIsBetweenAnotherSchedule || $validateIfNextEndTimeIsBetweenAnotherSchedule) || $validateIfNextScheduleIsOutRange))
                && ($dayWeek == $schedule_loop->day_week)
                ){
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
            ->paginate($request->get('per_page', 5), ['*'], 'page', $request->get('page', 1)); 

        // $this->authorize('viewAny', [Schedule::class, $buffet]);
        
        return view('schedule.index', ['buffet'=>$buffet, 'schedules'=>$schedules]); 
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

    
        $isConflicted = $this->verify_conflict(VerifyConflictDTO::makeFromRequest($request), $buffet);
        if($isConflicted){
            return redirect()->back()->withErrors(['start_time' => 'Existem conflitos de horário para o dia escolhido'])->withInput();
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

        $startDateTime = \Carbon\Carbon::parse($request->start_time);
        $endDateTime = $startDateTime->copy()->addMinutes($request->duration);

        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug])->with(['updated', 'Horário atualizado com sucesso!']);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $schedule_id = $this->hashids->decode($request->schedule);
        if(!$schedule_id) {
            return redirect()->back()->withErrors(['message'=>'Horário não encontrado'])->withInput();
        }
        $schedule_id = $schedule_id[0];

        $schedule = Schedule::where('id', $schedule_id)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['message'=>'Horário não encontrado'])->withInput();
        }
        $this->authorize('update', [Schedule::class, $schedule, $buffet]);

        if($schedule->status == ScheduleStatus::UNACTIVE->name) {
            return redirect()->back()->withErrors(['message'=>'Horário inativo. Altere seu status para ativo antes de tentar edita-lo'])->withInput();
        }

        return view('schedule.update', ['buffet'=>$buffet, 'schedule'=>$schedule]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->first();

        $schedule_id = $this->hashids->decode($request->schedule);
        if(!$schedule_id) {
            return redirect()->back()->withErrors(['message'=>'Horário não encontrado'])->withInput();
        }
        $schedule_id = $schedule_id[0];
        
        $schedule = Schedule::where('id', $schedule_id)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['message'=>'Horário não encontrado'])->withInput();
        }
        $this->authorize('update', [Schedule::class, $schedule, $buffet]);

        
        if($schedule->status == ScheduleStatus::UNACTIVE->name) {
            return redirect()->back()->withErrors(['message'=>'Horário inativo. Altere seu status para ativo antes de tentar edita-lo'])->withInput();
        }

        $isConflicted = $this->verify_conflict(VerifyConflictDTO::makeFromSchedule($schedule), $buffet);
        if($isConflicted){
            return redirect()->back()->withErrors(['start_time' => 'Existem conflitos de horário para o dia escolhido'])->withInput();
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


        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug])->with(['updated', 'Horário atualizado com sucesso!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $buffet_slug = $request->buffet;
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first(); 
        
        $schedule_id = $this->hashids->decode($request->schedule);
        if(!$schedule_id) {
            return redirect()->back()->withErrors(['message'=>'Horário não encontrado'])->withInput();
        }
        $schedule_id = $schedule_id[0];

        $schedule = Schedule::where('id', $schedule_id)->where('buffet_id', $buffet->id)->first(); 
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule'=>'Horário não encontrado'])->withInput();
        }
        $this->authorize('destroy', [Schedule::class, $schedule, $buffet]);

        $schedule->update(['status'=> ScheduleStatus::UNACTIVE->name]);


        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug]);
    }

    public function change_status(Request $request){
        $buffet_slug = $request->buffet; 
        $buffet = Buffet::where('slug', $buffet_slug)->get()->first();
        $schedule_id = $this->hashids->decode($request->schedule);
        if(!$schedule_id) {
            return redirect()->back()->withErrors(['message'=>'Horário não encontrado'])->withInput();
        }
        $schedule_id = $schedule_id[0];
        
        $schedule = $this->schedule->where('id', $schedule_id)->where('buffet_id', $buffet->id)->first();
        if(!$schedule){
            return redirect()->back()->withErrors(['schedule' => 'schedule not found'])->withInput();
        }
        $this->authorize('change_status', [Schedule::class, $schedule, $buffet]);

        if($request->status == ScheduleStatus::ACTIVE->name) {
            $isConflicted = $this->verify_conflict(VerifyConflictDTO::makeFromSchedule($schedule), $buffet);
            if($isConflicted){
                return redirect()->back()->withErrors(['start_time' => 'Existem conflitos de horário para o dia escolhido'])->withInput();
            }
        }
        
        $schedule->update(['status'=>$request->status]); 

        return redirect()->route('schedule.index', ['buffet'=>$buffet_slug]); 
    }
}
