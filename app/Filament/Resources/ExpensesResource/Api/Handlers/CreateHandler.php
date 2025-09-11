<?php
namespace App\Filament\Resources\ExpensesResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ExpensesResource;
use App\Filament\Resources\ExpensesResource\Api\Requests\CreateExpensesRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = ExpensesResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Expenses
     *
     * @param CreateExpensesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateExpensesRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}