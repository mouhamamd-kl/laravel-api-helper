<?php

namespace YourVendorName\YourPackageName;

use App\Console\Commands\MakeApiController;
use App\Console\Commands\MakeApiModel;
use App\Console\Commands\MakeApiRequest;
use App\Console\Commands\MakeApiResponse;
use App\Console\Commands\MakePaginationHelper;
use Illuminate\Support\ServiceProvider;

class ApiHelperProvider extends ServiceProvider
{
    public function register()
    {
        // Register the commands
        $this->commands([
            MakePaginationHelper::class,
            MakeApiResponse::class,
            MakeApiController::class,
            MakeApiModel::class,
            MakeApiRequest::class,
        ]);
    }

    public function boot()
    {
        // Publish stubs for customization
        $this->publishes([
            __DIR__ . '/stubs/api/custom-request.stub' => base_path('stubs/api/custom-request.stub'),
            __DIR__ . '/stubs/api/custom-controller.stub' => base_path('stubs/api/custom-controller.stub'),
            __DIR__ . '/stubs/api/custom-model.stub' => base_path('stubs/api/custom-model.stub'),
            __DIR__ . '/stubs/helpers/api-response.stub' => base_path('stubs/helpers/api-response.stub'),
            __DIR__ . '/stubs/helpers/pagination-helper.stub' => base_path('stubs/helpers/pagination-helper.stub'),
            // Add other stubs here if needed
        ], 'stubs');
    }
}
