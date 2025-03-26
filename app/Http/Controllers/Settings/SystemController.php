<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Settings\GeneralRequest;
use App\Models\Currency;

class SystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = collect([
            'app_name' => settings()->get('app_name'),
            'date_format' => settings()->get('date_format'),
            'time_format' => settings()->get('time_format'),
            'timezone' => settings()->get('timezone'),
            'logo_light' => settings()->get('logo_light'),
            'logo_dark' => settings()->get('logo_dark'),
            'currency_id' => settings()->get('currency_id'),
            'locale' => settings()->get('locale'),
            'stock_accounting_method' => settings()->get('stock_accounting_method'),
        ]);
        
        return response()->json($data, 200);
    }

    
    public function general()
    {
        $data = collect([
            'app_name' => settings()->get('app_name'),
            'company_name' => settings()->get('company_name'),
            'company_email' => settings()->get('company_email'),
            'company_phone' => settings()->get('company_phone'),
            'company_address' => settings()->get('company_address'),
            'date_format' => settings()->get('date_format'),
            'time_format' => settings()->get('time_format'),
            'timezone' => settings()->get('timezone'),
            'logo_light' => settings()->get('logo_light'),
            'logo_light_sm' => settings()->get('logo_light_sm'),
            'logo_dark' => settings()->get('logo_dark'),
            'logo_dark_sm' => settings()->get('logo_dark_sm'),
            'favicon' => (int)settings()->get('favicon'),
            'locale' => settings()->get('locale'),
        ]);

        return response()->json($data, 200);
    }
    
    public function generalUpdate(Request $request)
    {
        DB::beginTransaction();
        try{
            settings()->set('app_name', $request->app_name);
            settings()->set('company_name', $request->company_name);
            settings()->set('company_phone', $request->company_phone);
            settings()->set('company_email', $request->company_email);
            settings()->set('company_address', $request->company_address);
            settings()->set('date_format', $request->date_format);
            settings()->set('time_format', $request->time_format);
            settings()->set('timezone', $request->timezone);

            if($request->hasFile('logo_light')){
                deleteFile(settings()->get('logo_light'));

                settings()->set('logo_light', uploadFile($request->logo_light, 'logo', true));
            }

            if($request->hasFile('logo_dark')){
                deleteFile(settings()->get('logo_dark'));

                settings()->set('logo_dark', uploadFile($request->logo_dark, 'logo', true));
            }

            if($request->hasFile('logo_light_sm')){
                if(settings()->get('logo_light_sm')){
                    deleteFile(settings()->get('logo_light_sm'));
                }
                settings()->set('logo_light_sm', uploadFile($request->logo_light_sm, 'logo', true));
            }

            if($request->hasFile('logo_dark_sm')){
                if(settings()->get('logo_dark_sm')){
                    deleteFile(settings()->get('logo_dark_sm'));
                }
                settings()->set('logo_dark_sm', uploadFile($request->logo_dark_sm, 'logo', true));
            }

            if($request->hasFile('favicon')){
                if(settings()->get('favicon')){
                    deleteFile(settings()->get('favicon'));
                }
                settings()->set('favicon', uploadFile($request->favicon, 'logo', true));
            }
            settings()->set('currency_id', $request->currency_id);
            settings()->set('locale', $request->locale);
            settings()->set('invoicing_policy', $request->invoicing_policy);
            settings()->set('bill_controll', $request->bill_controll);
            settings()->set('src_location_id', $request->src_location_id);
            settings()->set('dest_location_id', $request->dest_location_id);

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }
    /**
     * Display a listing of the resource.
     */
    public function email()
    {
        $data = Collect([
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT'),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mail_encryption' => env('MAIL_ENCRYPTION'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS'),
            'mail_from_name' => env('MAIL_FROM_NAME')
        ]);

        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function emailUpdate(Request $request)
    {
        $rules = [
            // 'mail_host' => 'required',
            // 'mail_port' => 'required',
            // 'mail_username' => 'required',
            // 'mail_password' => 'required',
            // 'mail_encryption' => 'required',
            // 'mail_from_address' => 'required',
            // 'mail_from_name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }else{
            DB::beginTransaction();
            try{
                    putenv('MAIL_HOST=asasa');
                    putenv('MAIL_PORT'. $request->mail_port);
                    putenv('MAIL_USERNAME'. $request->mail_username);
                    putenv('MAIL_PASSWORD'. $request->mail_password);
                    putenv('MAIL_ENCRYPTION'. $request->mail_encryption);
                    putenv('MAIL_FROM_ADDRESS'. $request->mail_from_address);
                    putenv('MAIL_FROM_NAME'. $request->mail_from_name);
                    
                    // $this->setEnvironmentValue('APP_LOG_LEVEL', 'app.log_level', 'debug');


            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'result' => $e,
                ], 422);
            }
            DB::commit();
            return response()->json([
                'success' => true,
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function emailTest(Request $request)
    {
        //
    }

    
    public function accounting()
    {
        $data = collect([
            'account_revenue' => settings()->get('account_revenue'),
            'account_receivable' => settings()->get('account_receivable'),
            'account_sales_discount' => settings()->get('account_sales_discount'),
            'account_tax_collected' => settings()->get('account_tax_collected'),
            'account_expense' => settings()->get('account_expense'),
            'account_payable' => settings()->get('account_payable'),
            'account_tax_paid' => settings()->get('account_tax_paid'),
            'account_purchase_discount' => settings()->get('account_purchase_discount'),
        ]);

        return response()->json($data, 200);
    }
    
    public function accountingUpdate(Request $request)
    {
        DB::beginTransaction();
        try{
            settings()->set('account_revenue', $request->account_revenue);
            settings()->set('account_receivable', $request->account_receivable);
            settings()->set('account_sales_discount', $request->account_sales_discount);
            settings()->set('account_tax_collected', $request->account_tax_collected);
            settings()->set('account_expense', $request->account_expense);
            settings()->set('account_payable', $request->account_payable);
            settings()->set('account_tax_paid', $request->account_tax_paid);
            settings()->set('account_purchase_discount', $request->account_purchase_discount);

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }

    private function setEnvironmentValue($environmentName, $configKey, $newValue)
    {
        file_put_contents(App::environmentFilePath(), str_replace(
            $environmentName . '=' . Config::get($configKey),
            $environmentName . '=' . $newValue,
            file_get_contents(App::environmentFilePath())
        ));
    
        Config::set($configKey, $newValue);
    
        // Reload the cached config       
        if (file_exists(App::getCachedConfigPath())) {
            Artisan::call("config:cache");
        }
    }
}
