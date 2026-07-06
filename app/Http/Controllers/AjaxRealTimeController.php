<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AjaxRealTimeController extends Controller
{
    /**
     * Poll and fetch all new/historical messages for a chat partner.
     */
    public function fetchMessages($partnerId)
    {
        $userId = auth()->id();

        // Fetch all chronological messages between auth user and current chat partner
        $messages = DB::table('messages')
            ->where(function ($query) use ($userId, $partnerId) {
                $query->where('sender_id', $userId)->where('receiver_id', $partnerId);
            })
            ->orWhere(function ($query) use ($userId, $partnerId) {
                $query->where('sender_id', $partnerId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Dynamically mark incoming messages as read instantly on delivery
        DB::table('messages')
            ->where('sender_id', $partnerId)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'current_user_id' => $userId
        ]);
    }

    /**
     * Post a new message securely in the background using dynamic AJAX.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'message' => 'required|string',
            'student_id' => 'nullable|integer'
        ]);

        $columns = Schema::getColumnListing('messages');

        // Dynamic payload assembly based on active table fields
        $data = [
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'is_read' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        // Self-heal: Map body parameter to correct column name
        if (in_array('content', $columns)) {
            $data['content'] = $request->message;
        } else {
            $data['message'] = $request->message;
        }

        // Self-heal: Satisfy student foreign-key constraints
        if (in_array('student_id', $columns) && $request->filled('student_id')) {
            $data['student_id'] = $request->student_id;
        }

        $messageId = DB::table('messages')->insertGetId($data);

        return response()->json([
            'status' => 'success',
            'message_id' => $messageId,
            'message' => $request->message,
            'sender_id' => auth()->id()
        ]);
    }

    /**
     * Quietly poll for active emergencies corresponding to the user's role.
     */
    public function checkEmergency()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['has_emergency' => false]);
        }

        // Parent Role: Only pull alerts relative to their children
        if ($user->role === 'parent') {
            $studentIds = [];

            if (Schema::hasColumn('students', 'parent_id')) {
                $studentIds = DB::table('students')->where('parent_id', $user->id)->pluck('id')->toArray();
            } else if (Schema::hasTable('student_profiles')) {
                $studentIds = DB::table('student_profiles')->where('parent_id', $user->id)->pluck('student_id')->toArray();
            }

            if (!empty($studentIds)) {
                $activeAlert = DB::table('emergency_alerts')
                    ->whereIn('student_id', $studentIds)
                    ->where('status', 'active')
                    ->first();

                if ($activeAlert) {
                    $studentName = DB::table('students')->where('id', $activeAlert->student_id)->value('name');
                    return response()->json([
                        'has_emergency' => true,
                        'alert' => $activeAlert,
                        'student_name' => $studentName
                    ]);
                }
            }

            return response()->json(['has_emergency' => false]);
        }

        // Teachers, Admin, and GPK Khas: Monitor all active classroom emergencies globally
        $activeAlert = DB::table('emergency_alerts')
            ->where('status', 'active')
            ->first();

        if ($activeAlert) {
            $studentName = DB::table('students')->where('id', $activeAlert->student_id)->value('name');
            return response()->json([
                'has_emergency' => true,
                'alert' => $activeAlert,
                'student_name' => $studentName
            ]);
        }

        return response()->json(['has_emergency' => false]);
    }

    /**
     * Mark an emergency alert resolved in real-time.
     */
    public function acknowledgeEmergency($alertId)
    {
        DB::table('emergency_alerts')
            ->where('id', $alertId)
            ->update([
                'status' => 'resolved',
                'resolved_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Emergency acknowledged and resolved!'
        ]);
    }
}
