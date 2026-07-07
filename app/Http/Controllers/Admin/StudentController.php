<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['classRoom', 'parent'])
            ->orderBy('name')
            ->get();

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classes = ClassRoom::orderBy('class_name')->get();
        $parents = User::where('role', 'parent')->orderBy('name')->get();

        $relationshipOptions = [
            'Mother', 'Father', 'Guardian',
            'Grandmother', 'Grandfather',
            'Sibling', 'Uncle', 'Aunt', 'Other',
        ];

        return view('admin.students.create', compact(
            'classes', 'parents', 'relationshipOptions'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'class_id'      => 'required|exists:classes,id',
            'mykid_number'  => 'nullable|string|unique:students,mykid_number',
            'date_of_birth' => 'nullable|date',
            'parent_id'     => 'nullable|exists:users,id',
        ]);

        // Get teacher from the class
        $class     = ClassRoom::find($request->class_id);
        $teacherId = $class?->teacher_id;

        $student = Student::create([
            'name'          => $request->name,
            'class_id'      => $request->class_id,
            'teacher_id'    => $teacherId,
            'parent_id'     => $request->parent_id ?: null,
            'diagnosis'     => 'Autism',
            'mykid_number'  => $request->mykid_number ?: null,
            'date_of_birth' => $request->date_of_birth ?: null,
        ]);

        // Create empty profile
        StudentProfile::create(['student_id' => $student->id]);

        ActivityLog::record(
            Auth::id(),
            'Student Created',
            "Admin enrolled student: {$student->name} in class {$class->class_name}"
        );

        return redirect()->route('admin.students.index')
            ->with('success', "Student '{$student->name}' enrolled successfully.");
    }

    public function show(Student $student)
    {
    $student->load(['profile', 'classRoom.teacher', 'parent']);

    $recentLogs = $student->behaviourLogs()
        ->with('teacher')
        ->latest('logged_at')
        ->take(10)
        ->get();

    $classes = ClassRoom::orderBy('class_name')->get();

    return view('admin.students.show', compact(
        'student', 'recentLogs', 'classes'
    ));
    }

    public function destroy(Student $student)
    {
        $name = $student->name;
        ActivityLog::record(
            Auth::id(),
            'Student Deleted',
            "Admin removed student: {$name}"
        );

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', "Student '{$name}' removed.");
    }

    public function changeClass(Request $request, Student $student)
    {
    $request->validate([
        'class_id' => 'required|exists:classes,id',
    ]);

    $newClass  = ClassRoom::find($request->class_id);
    $oldClass  = $student->classRoom?->class_name ?? 'none';

    $student->update([
        'class_id'   => $newClass->id,
        'teacher_id' => $newClass->teacher_id,
    ]);

    ActivityLog::record(
        Auth::id(),
        'Student Class Changed',
        "Admin moved {$student->name} from {$oldClass} to {$newClass->class_name}"
    );

    return back()->with('success',
        "{$student->name} moved to {$newClass->class_name}.");
    }
}
