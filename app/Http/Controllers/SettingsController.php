<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index(){
        $settings = Settings::firstOrFail();

        return view('settings.index', compact('settings'));
        
    }

    public function edit(){

        return view('settings.edit');
    }

    public function store(Request $request){
        // dd($request);
        // dd('hello there');
        // $request['allowGuarantor'] = filter_var($request['allowGuarantor'], FILTER_VALIDATE_BOOLEAN);
        $request['allowGuarantor'] = $request['allowGuarantor'] ? true : false;
        $validated = $request->validate([
            'orgName' => ['required'],
            'location' => ['required'],
            'email' => ['required','email'],
            'phone' => ['required'],
            'minSavings' => ['required', 'numeric', 'min:0'],
            'interestRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'loanDuration' => ['required', 'integer', 'min:1'],
            'loanType' => ['required', 'string', 'in:fixed,reducing'],
            'loanMaxAmount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'allowGuarantor' => ['sometimes', 'boolean'],
            'minGuarantor' => ['required_if:allowGuarantor,true', 'integer', 'min:1'],
            'maxGuarantor' => ['nullable', 'integer', 'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $min = $request->input('minGuarantor');
                    if (!is_null($value) && $value < $min) {
                        $fail('The max guarantor must be greater than or equal to the min guarantor.');
                    }
                }
            ],
            'minSavingsGuarantor' => ['nullable', 'numeric', 'min:0', 'required_if:allowGuarantor,true'],
            // user validation
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'admin_email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
            'password' => 'required|string|min:8|confirmed',
            'Date_OF_Birth' => ['required','date','before:' . now()->subYears(18)->format('Y-m-d')],
            'Address' => 'required|string',
        ]);

        try {
            
            $settings = Settings::create([
                'organization_name' => $validated['orgName'],
                'organization_address' => $validated['location'],
                'organization_email' => $validated['email'],
                'organization_phone' => $validated['phone'],
                'min_savings' => $validated['minSavings'],
                'interest_rate' => $validated['interestRate'],
                'loan_duration' => $validated['loanDuration'],
                'loan_type' => $validated['loanType'],
                'loan_max_amount' => $validated['loanMaxAmount'],
                'currency' => $validated['currency'],
                'allow_guarantor' => $validated['allowGuarantor'],
                'min_guarantor' => $validated['minGuarantor'],
                'max_guarantor' => $validated['maxGuarantor'] ?? $validated['minGuarantor'],
                'min_savings_guarantor' => $validated['minSavingsGuarantor']
            ]);
            
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->admin_email,
                'phone_number' => $request->phone_number,
                'Date_OF_Birth' => $request->Date_OF_Birth,
                'Address' => $request->Address,
                'password' => Hash::make($request->password),
                'status' => 'active',
                'role' => 'admin'
            ]);

            // echo '8';
            // sending verification email to the user 
            $user->sendEmailVerificationNotification();
            // echo '9';
            // $user->notify(new WelcomeNotification());
            Auth::login($user);
            return response()->json(['message' => 'Configuration saved successfully.', 'status' => 'success'], status: 200);
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error saving settings: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while saving the data.', 'status' => 'success'], 500);
        }
    }

        public function update(Request $request)
    {
        // Get the existing settings (assuming single record)
        $settings = Settings::firstOrFail();

        // Convert checkbox value to boolean
        $request->merge([
            'allow_guarantor' => $request->has('allow_guarantor')
        ]);

        $validated = $request->validate([
            // Organization Information
            'organization_name' => 'required|string|max:255',
            'organization_email' => 'required|email|max:255',
            'organization_phone' => 'required|string|max:20',
            'organization_address' => 'required|string|max:255',
            'organization_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Financial Configuration
            'min_savings' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'loan_duration' => 'required|integer|min:1',
            'loan_type' => 'required|in:fixed,reducing',
            'loan_max_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',

            // Guarantor Requirements
            'allow_guarantor' => 'sometimes|boolean',
            'min_guarantor' => 'required_if:allow_guarantor,true|integer|min:1',
            'max_guarantor' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $value < $request->input('min_guarantor')) {
                        $fail('Max guarantors must be greater than or equal to min guarantors.');
                    }
                }
            ],
            'min_savings_guarantor' => 'required_if:allow_guarantor,true|numeric|min:0',
        ]);

        try {
            // Handle logo upload
            if ($request->hasFile('organization_logo')) {
                // Delete old logo if exists
                if ($settings->organization_logo) {
                    Storage::disk('public')->delete($settings->organization_logo);
                }
                // Store new logo
                $validated['organization_logo'] = $request->file('organization_logo')
                    ->store('logos', 'public');
            } else {
                // Keep existing logo if not updated
                $validated['organization_logo'] = $settings->organization_logo;
            }

            // Update settings
            $settings->update([
                // Organization Info
                'organization_name' => $validated['organization_name'],
                'organization_email' => $validated['organization_email'],
                'organization_phone' => $validated['organization_phone'],
                'organization_address' => $validated['organization_address'],
                'organization_logo' => $validated['organization_logo'],

                // Financial Configuration
                'min_savings' => $validated['min_savings'],
                'interest_rate' => $validated['interest_rate'],
                'loan_duration' => $validated['loan_duration'],
                'loan_type' => $validated['loan_type'],
                'loan_max_amount' => $validated['loan_max_amount'],
                'currency' => $validated['currency'],

                // Guarantor Requirements
                'allow_guarantor' => $validated['allow_guarantor'],
                'min_guarantor' => $validated['allow_guarantor'] ? $validated['min_guarantor'] : null,
                'max_guarantor' => $validated['allow_guarantor'] 
                    ? ($validated['max_guarantor'] ?? $validated['min_guarantor']) 
                    : null,
                'min_savings_guarantor' => $validated['allow_guarantor'] 
                    ? $validated['min_savings_guarantor'] 
                    : null,
            ]);

            // return response()->json([
            //     'message' => 'Settings updated successfully',
            //     'status' => 'success'
            // ], 200);

            return redirect()->back()->with('success', 'Settings updated successfully');

        } catch (\Exception $e) {
            Log::error('Settings update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }
}
