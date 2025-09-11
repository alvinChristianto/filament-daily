<?php
namespace App\Filament\Resources\ExpensesResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ExpensesResource;
use App\Filament\Resources\ExpensesResource\Api\Requests\UpdateExpensesRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = ExpensesResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Expenses
     *
     * @param UpdateExpensesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateExpensesRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}