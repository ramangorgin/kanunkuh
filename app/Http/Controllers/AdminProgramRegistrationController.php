<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProgramRegistrationController extends Controller
{
    /**
     * Show all registrations for a specific program
     */
    public function index(Program $program)
    {
        $registrations = ProgramRegistration::with(['user.profile', 'payment'])
            ->where('program_id', $program->id)
            ->latest()
            ->get();
            
        return view('admin.program_registrations.index', compact('program', 'registrations'));
    }

    /**
     * Approve a registration (independent of payment)
     */
    public function approve(Program $program, $registrationId)
    {
        try {
            \Log::info('Approve registration called', [
                'program_id' => $program->id,
                'registration_id' => $registrationId
            ]);
            
            $registration = ProgramRegistration::where('id', $registrationId)
                ->where('program_id', $program->id)
                ->first();
            
            if (!$registration) {
                \Log::error('Registration not found', [
                    'program_id' => $program->id,
                    'registration_id' => $registrationId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت‌نام یافت نشد.'
                ], 404);
            }
            
            \Log::info('Registration found', [
                'registration' => $registration->toArray()
            ]);
            
            $registration->status = 'approved';
            $saved = $registration->save();
            
            \Log::info('Registration saved', ['saved' => $saved, 'status' => $registration->status]);
            
            return response()->json([
                'success' => true, 
                'message' => 'ثبت‌نام تأیید شد.',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status
                ]
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error in approve', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطای دیتابیس: ' . $e->getMessage(),
                'error_type' => 'database'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Error in approve registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطا: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Reject a registration
     */
    public function reject(Program $program, $registrationId)
    {
        try {
            \Log::info('Reject registration called', [
                'program_id' => $program->id,
                'registration_id' => $registrationId
            ]);
            
            $registration = ProgramRegistration::where('id', $registrationId)
                ->where('program_id', $program->id)
                ->first();
            
            if (!$registration) {
                \Log::error('Registration not found for reject', [
                    'program_id' => $program->id,
                    'registration_id' => $registrationId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت‌نام یافت نشد.'
                ], 404);
            }
            
            $registration->status = 'rejected';
            $registration->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'ثبت‌نام رد شد.',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in reject registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطا: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    /**
     * Cancel a registration
     */
    public function cancel(Program $program, $registrationId)
    {
        try {
            \Log::info('Cancel registration called', [
                'program_id' => $program->id,
                'registration_id' => $registrationId
            ]);
            
            $registration = ProgramRegistration::where('id', $registrationId)
                ->where('program_id', $program->id)
                ->first();
            
            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'ثبت‌نام یافت نشد.'
                ], 404);
            }
            
            $registration->status = 'cancelled';
            $registration->save();
            
            return response()->json([
                'success' => true, 
                'message' => 'ثبت‌نام لغو شد.',
                'data' => [
                    'id' => $registration->id,
                    'status' => $registration->status
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in cancel registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطا: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }
}

