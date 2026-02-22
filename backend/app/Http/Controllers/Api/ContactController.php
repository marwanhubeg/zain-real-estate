<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة جهات الاتصال'
            ], 403);
        }

        $query = Contact::query();

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب النوع
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $contacts = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'تم جلب جهات الاتصال بنجاح',
            'data' => ContactResource::collection($contacts),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'sometimes|in:general,support,complaint,suggestion',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['status'] = 'new';
        $data['priority'] = $this->determinePriority($request->message);
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $contact = Contact::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً',
            'data' => new ContactResource($contact)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Contact $contact)
    {
        // فقط للمشرفين أو صاحب الرسالة
        if ($request->user()->role !== 'admin' && $request->user()->email !== $contact->email) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة هذه الرسالة'
            ], 403);
        }

        // تحديث الحالة إلى مقروءة إذا كان المشرف يشاهدها
        if ($request->user()->role === 'admin' && $contact->status === 'new') {
            $contact->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الرسالة بنجاح',
            'data' => new ContactResource($contact)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتحديث هذه الرسالة'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:new,read,replied,closed',
            'priority' => 'sometimes|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'reply_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        if ($request->has('reply_message')) {
            $data['replied_at'] = now();
            $data['replied_by'] = $request->user()->id;
            $data['status'] = 'replied';
        }

        $contact->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الرسالة بنجاح',
            'data' => new ContactResource($contact)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Contact $contact)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذه الرسالة'
            ], 403);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الرسالة بنجاح'
        ]);
    }

    /**
     * Reply to contact message
     */
    public function reply(Request $request, Contact $contact)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالرد على هذه الرسالة'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'reply_message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'الرد مطلوب',
                'errors' => $validator->errors()
            ], 422);
        }

        $contact->reply($request->reply_message, $request->user()->id);

        // هنا يمكن إرسال إيميل بالرد

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الرد بنجاح',
            'data' => new ContactResource($contact)
        ]);
    }

    /**
     * Close contact message
     */
    public function close(Request $request, Contact $contact)
    {
        // فقط للمشرفين
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإغلاق هذه الرسالة'
            ], 403);
        }

        $contact->close($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'تم إغلاق الرسالة بنجاح',
            'data' => new ContactResource($contact)
        ]);
    }

    /**
     * Determine priority based on message content
     */
    private function determinePriority($message)
    {
        $highPriorityKeywords = ['عاجل', 'مهم', 'طارئ', 'شكوى', 'مشكلة', 'ضروري'];
        foreach ($highPriorityKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'high';
            }
        }
        return 'medium';
    }
}
