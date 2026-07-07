<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with(['teacher', 'students'])
            ->orderBy('class_name')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name'    => 'required|string|max:50',
            'teacher_id'    => 'required|exists:users,id',
            'academic_year' => 'required|integer|min:2020|max:2030',
        ]);

        $class = ClassRoom::create([
            'class_name'    => $request->class_name,
            'teacher_id'    => $request->teacher_id,
            'academic_year' => $request->academic_year,
        ]);

        ActivityLog::record(
            Auth::id(),
            'Class Created',
            "Admin created class: {$class->class_name}"
        );

        return redirect()->route('admin.classes.index')
            ->with('success', "Class '{$class->class_name}' created.");
    }

    public function show(ClassRoom $class)
    {
    $class->load(['teacher', 'students.parent', 'students.profile']);

    $allClasses = ClassRoom::where('id', '!=', $class->id)
        ->orderBy('class_name')
        ->get();

    return view('admin.classes.show', compact('class', 'allClasses'));
    }

    public function destroy(ClassRoom $class)
    {
        $name = $class->class_name;
        ActivityLog::record(
            Auth::id(),
            'Class Deleted',
            "Admin deleted class: {$name}"
        );

        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', "Class '{$name}' deleted.");
    }
}
