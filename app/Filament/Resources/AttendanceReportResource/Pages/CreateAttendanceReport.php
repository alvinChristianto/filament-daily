<?php

namespace App\Filament\Resources\AttendanceReportResource\Pages;

use App\Filament\Resources\AttendanceReportResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateAttendanceReport extends CreateRecord
{
    protected static string $resource = AttendanceReportResource::class;

    // protected function afterValidate(): void
    // {
    //     $test = 1;

    //     if ($test === 1) {
    //         Notification::make()
    //             ->title('Gagal menyimpan data')
    //             ->body('Data tidak dapat disimpan karena "Some Field" berisi nilai terlarang.')
    //             ->danger() // Or warning(), success(), info()
    //             ->send();

    //         // 2. IMPORTANT: Throw an exception to halt the saving process.
    //         //    This prevents the method from reaching its return statement and effectively cancels the save.
    //         throw new \Exception('Data not saved due to invalid combination.');

    //     }
    // }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $defaultInTime = Carbon::now()->setTime(8, 0, 0);
        $defaultOutTime = Carbon::now()->setTime(17, 0, 0);
        $is_present = $data['is_present'];
        if ($is_present) {
            $data['type_absence'] = 'MASUK';
            $data['in_time'] = $defaultInTime->format('Y-m-d H:i:s');
            $data['out_time'] = $defaultOutTime->format('Y-m-d H:i:s');
        }
        // if ($test === 1) {
        //     Notification::make()
        //         ->title('Gagal menyimpan data')
        //         ->body('Data tidak dapat disimpan karena "Some Field" berisi nilai terlarang.')
        //         ->danger() // Or warning(), success(), info()
        //         ->send();

        //     // 2. IMPORTANT: Throw an exception to halt the saving process.
        //     //    This prevents the method from reaching its return statement and effectively cancels the save.
        //     throw new ValidationException([
        //         'some_field' => 'Nilai ini tidak diizinkan.' // Optional: attach error to a specific field
        //     ]);
        // }

        // dd($data);
        return $data;
    }

    protected function afterCreate(): void
    {
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Simpan Data Absensi'),
            $this->getCancelFormAction()
        ];
    }
}
