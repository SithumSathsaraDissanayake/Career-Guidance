<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['user_id'];$receiver_id = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;

// AJAX: Send Message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_msg'])) {
    $msg_text = trim($_POST['message']);
    if (!empty($msg_text) &&$receiver_id > 0) {
        $stmt =$conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (:s, :r, :m)");
        $stmt->execute(['s' =>$current_user, 'r' => $receiver_id, 'm' =>$msg_text]);
    }
    exit();
}

// AJAX: Fetch Messages
if (isset($_GET['fetch_messages'])) {
    $stmt =$conn->prepare("SELECT * FROM messages WHERE (sender_id = :u1 AND receiver_id = :r1) OR (sender_id = :r2 AND receiver_id = :u2) ORDER BY created_at ASC");
    $stmt->execute(['u1' => $current_user, 'r1' =>$receiver_id, 'r2' => $receiver_id, 'u2' =>$current_user]);
    $messages =$stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($messages)) {
        echo '<div class="text-center text-muted my-auto"><small>No messages yet. Start the conversation!</small></div>';
    } else {
        foreach ($messages as $m) {$is_me = ($m['sender_id'] ==$current_user);
            echo '<div class="d-flex ' . ($is_me ? 'justify-content-end' : 'justify-content-start') . ' mb-2">';
            echo '<div class="p-2 px-3 rounded-3 ' . ($is_me ? 'bg-primary text-white' : 'bg-white text-dark border shadow-sm') . '" style="max-width: 75%;">';
            echo htmlspecialchars($m['message']);
            echo '<div class="small text-end mt-1 ' . ($is_me ? 'text-white-50' : 'text-muted') . '" style="font-size:10px;">' . date('h:i A', strtotime($m['created_at'])) . '</div>';
            echo '</div></div>';
        }
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuturePath - Real-time Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f3f4f6; }
        .chat-box { height: 420px; background-color: #f8fafc; overflow-y: auto; }
    </style>
</head>
<body>

<div class="container py-4" style="max-width: 700px;">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 fw-bold"><i class="bi bi-chat-dots-fill text-info me-2"></i>Live Consultation Chat</h6>
            <a href="javascript:history.back()" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
        </div>
        
        <!-- Chat Box Window -->
        <div class="card-body p-3 chat-box d-flex flex-column" id="chat-box">
            <!-- Messages load here -->
        </div>

        <!-- Input Box -->
        <div class="card-footer bg-white border-top p-3">
            <form id="chat-form" class="d-flex gap-2">
                <input type="text" id="msg-input" class="form-control rounded-pill px-3" placeholder="Type your message here..." required autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-circle px-3"><i class="bi bi-send-fill"></i></button>
            </form>
        </div>
    </div>
</div>

<script>
const receiverId = <?php echo $receiver_id; ?>;
const chatBox = document.getElementById('chat-box');
let isInitialLoad = true;

function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}

function loadMessages() {
    // Check if the user is currently scrolled near the bottom before update
    const isAtBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 50;

    fetch(`chat.php?receiver_id=${receiverId}&fetch_messages=1`)
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;
            
            // Scroll down on initial load or if user is already at the bottom
            if (isInitialLoad || isAtBottom) {
                scrollToBottom();
                isInitialLoad = false;
            }
        });
}

// Handle Form Submission
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('msg-input');
    const msg = input.value.trim();
    if(!msg) return;

    const formData = new FormData();
    formData.append('send_msg', '1');
    formData.append('message', msg);

    fetch(`chat.php?receiver_id=${receiverId}`, {
        method: 'POST',
        body: formData
    }).then(() => {
        input.value = '';
        loadMessages();
        setTimeout(scrollToBottom, 100); // Ensure smooth scroll down after sending
    });
});

setInterval(loadMessages, 2000);
loadMessages();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>