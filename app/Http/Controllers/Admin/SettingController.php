<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'school_name'         => Setting::get('school_name', 'مدرسة التميز الذكية'),
            'system_enabled'      => Setting::get('system_enabled', '1'),
            'school_logo'         => Setting::get('school_logo'),
            'contact_email'       => Setting::get('contact_email'),
            'contact_phone'       => Setting::get('contact_phone'),
            'welcome_message'     => Setting::get('welcome_message', 'مرحباً بك في نظام امتحانات القبول'),
            'show_results_instantly' => Setting::get('show_results_instantly', '1'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name'     => 'required|string|max:200',
            'system_enabled'  => 'required|boolean',
            'school_logo'     => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'contact_email'   => 'nullable|email',
            'contact_phone'   => 'nullable|string',
            'welcome_message' => 'nullable|string',
            'show_results_instantly' => 'required|boolean',
        ]);

        Setting::set('school_name', $request->school_name);
        Setting::set('system_enabled', $request->system_enabled);
        Setting::set('contact_email', $request->contact_email);
        Setting::set('contact_phone', $request->contact_phone);
        Setting::set('welcome_message', $request->welcome_message);
        Setting::set('show_results_instantly', $request->show_results_instantly);

        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('school_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('school_logo')->store('settings', 'public');
            Setting::set('school_logo', $path);
        }

        return back()->with('success', 'تم تحديث إعدادات النظام بنجاح.');
    }
}
