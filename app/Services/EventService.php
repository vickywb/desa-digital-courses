<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\Event;
use App\Repository\EventRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function __construct(
        private EventRepository $eventRepository,
        private FileService $fileService
    ) {}

    public function createEvent(array $data, ?UploadedFile $image = null): Event
    {
        try {
            return DB::transaction(function () use ($data, $image) {
                $fileId = $this->fileService->handleUploadAndSave($image, 'file/events')?->id;

                $event = $this->eventRepository->save(new Event([
                    ...$data,
                    'file_id' => $fileId,
                ]));

                LoggerHelper::info('Event created successfully', [
                    'event_id' => $event->id,
                    'title' => $event->title,
                ]);

                return $event;
            });

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to create event',
                ['error_message' => $th->getMessage()]
            );

            throw $th;
        }
    }

    public function updateEvent(array $data, ?UploadedFile $eventFile, Event $event): Event
    {
        try {
            return DB::transaction(function () use ($data, $eventFile, $event) {
                $oldFileId = $event->file_id;
                $newFileId = $eventFile
                    ? $this->fileService->handleUploadAndSave($eventFile, 'file/events')?->id
                    : $oldFileId;

                $event->fill([
                    ...$data,
                    'file_id' => $newFileId,
                ]);

                $event = $this->eventRepository->save($event);

                if ($eventFile && $oldFileId) {
                    $this->fileService->deleteFile($oldFileId);
                }

                LoggerHelper::info('Event updated successfully', [
                    'event_id' => $event->id,
                    'title' => $event->title,
                ]);

                return $event;
            });

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to update event', [
                'event_id' => $event->id,
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }

    public function deleteEvent(Event $event): void
    {
        try {
            DB::transaction(function () use ($event) {
                $fileId = $event->file_id;

                $event->delete();

                if ($fileId) {
                    $this->fileService->deleteFile($fileId);
                }

                LoggerHelper::info('Event deleted successfully', [
                    'event_id' => $event->id,
                    'title' => $event->title,
                ]);
            });
        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to delete event', [
                'event_id' => $event->id,
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }
}
