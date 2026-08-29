<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $enquiries = Enquiry::with('property')
            ->when($request->input('type'), fn ($q, $v) => $q->where('type', $v))
            ->when($request->boolean('unread'), fn ($q) => $q->unread())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries'   => $enquiries,
            'unreadCount' => Enquiry::unread()->count(),
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        $enquiry->forceFill(['read_at' => $enquiry->read_at ?? now()])->save();

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')->with('status', 'Enquiry deleted.');
    }
}
