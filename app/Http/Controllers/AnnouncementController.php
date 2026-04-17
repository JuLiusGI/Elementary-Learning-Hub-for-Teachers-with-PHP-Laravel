<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Announcement::with('creator')
            ->orderByDesc('is_pinned')
            ->byPriority()
            ->orderByDesc('created_at');

        if ($user->isTeacher()) {
            $query->published();
        }

        $announcements = $query->paginate(15);

        // Get IDs of read announcements for current user
        $readIds = $user->id
            ? Announcement::whereHas('reads', fn ($q) => $q->where('user_id', $user->id))
                ->pluck('id')
                ->toArray()
            : [];

        return view('announcements.index', compact('announcements', 'readIds'));
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);

        return view('announcements.create');
    }

    public function store(AnnouncementRequest $request)
    {
        $this->authorize('create', Announcement::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $announcement = Announcement::create($data);

        $this->auditLog->log('announcement.created', $announcement, null, $data);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        $announcement->load('creator');
        $announcement->markAsRead(auth()->user());

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        return view('announcements.edit', compact('announcement'));
    }

    public function update(AnnouncementRequest $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $oldValues = $announcement->only(['title', 'content', 'priority', 'is_pinned', 'published_at', 'expires_at']);
        $announcement->update($request->validated());

        $this->auditLog->log('announcement.updated', $announcement, $oldValues, $request->validated());

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $this->auditLog->log('announcement.deleted', $announcement, $announcement->toArray());
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
