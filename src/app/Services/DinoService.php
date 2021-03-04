<?php

namespace App\Services;

use App\Models\Dinosaur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DinoService
{
    /**
     * Gets data from the DinoPark NULDS Feed
     *
     * @return mixed
     */
    public function getFeed()
    {
        $endpoint = config('services.dinopark.nulds.endpoint');

        $response = Http::get($endpoint);

        return json_decode($response, true);
    }

    /**
     * @return array
     */
    public function processFeed(): array
    {
        $events = $this->getFeed();
        $maintenanceEvents = [];

        usort($events, function ($eventOne, $eventTwo) {
            $dateTimeOne = Carbon::parse($eventOne["time"])->toDateTimeString();
            $dateTimeTwo = Carbon::parse($eventTwo["time"])->toDateTimeString();

            if ($dateTimeOne > $dateTimeTwo)
                return 1;
            else if ($dateTimeOne < $dateTimeTwo)
                return -1;
            else
                return 0;
        });

        foreach ($events as $event) {
            if ($event['kind'] == 'dino_added') {
                $dinosaur = Dinosaur::withTrashed()->firstWhere('dinosaur_id', $event['id']);
                if ($dinosaur == null) {
                    $dateTime = Carbon::parse($event["time"])->toDateTimeString();
                    Dinosaur::create([
                        'dinosaur_id' => $event['id'],
                        'name' => $event['name'],
                        'kind' => $event['kind'],
                        'species' => $event['species'],
                        'gender' => $event['gender'],
                        'digestion_period_in_hours' => $event['digestion_period_in_hours'],
                        'herbivore' => $event['herbivore'],
                        'park_id' => $event['park_id'],
                        'created_at' => $dateTime,
                        'updated_at' => $dateTime,
                    ]);
                }
            }
        }

        foreach ($events as $event) {
            if ($event['kind'] != 'dino_added' && $event['kind'] != 'maintenance_performed') {
                $dateTime = Carbon::parse($event["time"])->toDateTimeString();
                $dinosaur = Dinosaur::firstWhere('dinosaur_id', $event['dinosaur_id']);
                if ($dinosaur) {
                    $dinosaur->kind = $event['kind'] ?? $dinosaur->kind;
                    $dinosaur->park_id = $event['park_id'] ?? 1;
                    $dinosaur->location = $event['location'] ?? $dinosaur->location;
                    $dinosaur->updated_at = $dateTime ?? $dinosaur->updated_at;

                    if ($event['kind'] == 'dino_removed') {
                        $dinosaur->delete();
                        $dinosaur->deleted_at = $dateTime ?? $dinosaur->deleted_at;
                    }
                    $dinosaur->save();
                }
            } elseif ($event['kind'] == 'maintenance_performed') {
                Log::info(print_r($event, true));
                $maintenanceEvents[] = $event;
            }
        }

        return $this->processMaintenanceEvents($maintenanceEvents);
    }

    public function processMaintenanceEvents(array $events)
    {
        $output = [];

        foreach ($events as $event) {
            $dinosaur = Dinosaur::firstWhere('location', $event['location']);

            if ($dinosaur && $dinosaur->herbivore == false && $dinosaur->location == $event['location']) {
                if ($dinosaur->kind != 'dino_fed') {
                    $output[] = ['location' => $event['location'], 'is_safe' => false];
                } else {
                    $maintenanceDateTime = Carbon::parse($event["time"])->toDateTimeString();
                    if ($dinosaur->updated_at > $maintenanceDateTime) {
                        $output[] = ['location' => $event['location'], 'is_safe' => true];
                    } else {
                        if ($maintenanceDateTime <= $dinosaur->updated_at->addHours($dinosaur->digestion_period_in_hours)->toDateTimeString()) {
                            $output[] = ['location' => $event['location'], 'is_safe' => false];
                        } else {
                            $output[] = ['location' => $event['location'], 'is_safe' => true];
                        }
                    }
                }

            } else {
                $output[] = ['location' => $event['location'], 'is_safe' => true];
            }
        }

        return $output;
    }
}