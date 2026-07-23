<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollment;

class PageController extends Controller
{
    /**
     * Display the About Us page.
     */
    public function about()
    {
        // Get statistics to display on the About page
        $coursesCount = Course::where('is_published', true)->count();
        $studentsCount = User::where('role', 'student')->count();
        $instructorsCount = User::where('role', 'instructor')->count();
        $enrollmentsCount = CourseEnrollment::count();

        return view('pages.about', compact('coursesCount', 'studentsCount', 'instructorsCount', 'enrollmentsCount'));
    }

    /**
     * Display the Contact Us page.
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Handle the contact form submission.
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'name.required' => 'حقل الاسم مطلوب.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح.',
            'subject.required' => 'حقل الموضوع مطلوب.',
            'message.required' => 'حقل الرسالة مطلوب.',
            'message.min' => 'يجب ألا تقل الرسالة عن 10 أحرف.',
        ]);

        // In a real application, you might send an email or store this in a database.
        // For now, we will return a success flash message.
        return back()->with('success', 'تم إرسال رسالتك بنجاح! سنتواصل معك في أقرب وقت ممكن.');
    }
}
