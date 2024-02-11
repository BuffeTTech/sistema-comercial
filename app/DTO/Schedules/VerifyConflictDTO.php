<?php

namespace App\DTO\Schedules;

use App\Enums\DayWeek;
use App\Enums\ScheduleStatus;
use App\Models\Schedule;
use DateTime;
use Illuminate\Http\Request;

class VerifyConflictDTO {
    public function __construct(
        public DateTime $start_time,
        public int $duration,
        public DayWeek $day_week,
        public ScheduleStatus $status,
        public ?int $id = null,
    ) {}

    public static function makeFromRequest(Request $request): self {
        return new self(
            new DateTime($request->start_time),
            (int) $request->duration,
            DayWeek::getEnumByName($request->day_week),
            $request->status ? ScheduleStatus::getEnumByName($request->status) : ScheduleStatus::ACTIVE,
            $request->id !== null ? (int) $request->id : null
        );
    }

    public static function makeFromSchedule(Schedule $schedule): self {
        return new self(
            new DateTime($schedule->start_time),
            (int) $schedule->duration,
            DayWeek::getEnumByName($schedule->day_week),
            ScheduleStatus::getEnumByName($schedule->status) ?? ScheduleStatus::ACTIVE,
            (int) $schedule->id
        );
    }
}