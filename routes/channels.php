use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;
use App\Models\Contribution;
use App\Models\Meeting;
use App\Models\User;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::with('participants')->find($chatId);
    return $chat && $chat->participants->contains($user->id);
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('contribution.{id}', function (User $user, int $id) {
    $contribution = Contribution::with('meeting.participants')->find($id);
    if (!$contribution) {
        return false;
    }
    if ($user->isAdmin() || $user->id === $contribution->organizer_id || $user->id === $contribution->approver_id) {
        return true;
    }
    if ($contribution->meeting && $contribution->meeting->participants()->where('users.id', $user->id)->exists()) {
        return true;
    }
    return false;
});

Broadcast::channel('meeting.{id}', function (User $user, int $id) {
    $meeting = Meeting::with('participants')->find($id);
    if (!$meeting) {
        return false;
    }
    if ($user->isAdmin() || $user->id === $meeting->organizer_id) {
        return true;
    }
    return $meeting->participants->contains('id', $user->id);
});
