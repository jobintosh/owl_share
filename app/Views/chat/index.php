<div class="container-fluid h-100">
   <div class="row h-100">
       <!-- Chat List -->
       <div class="col-md-4 col-lg-3 border-end p-0">
           <div class="chat-list">
               <div class="chat-list-header p-3 border-bottom">
                   <div class="d-flex justify-content-between align-items-center">
                       <h5 class="mb-0">ข้อความ</h5>
                       <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
                           <i class="fas fa-plus me-1"></i> สนทนาใหม่
                       </button>
                   </div>
               </div>

               <!-- รายการแชทที่มีอยู่ -->
               <div class="chat-list-body">
                   <?php if (empty($chats)): ?>
                       <div class="text-center text-muted p-4">
                           <p>ยังไม่มีข้อความ</p>
                           <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
                               <i class="fas fa-plus me-1"></i> เริ่มสนทนาใหม่
                           </button>
                       </div>
                   <?php else: ?>
                       <?php foreach ($chats as $chat): ?>
                           <div class="chat-item p-3 border-bottom" 
                               data-user-id="<?= $chat['sender_id'] == session()->get('user_id') ? $chat['recipient_id'] : $chat['sender_id'] ?>">
                               <div class="d-flex align-items-center">
                                   <div class="chat-item-avatar me-3">
                                       <?php if (!empty($chat['avatar'])): ?>
                                           <img src="<?= base_url($chat['avatar']) ?>" 
                                               class="rounded-circle" 
                                               width="50" height="50"
                                               alt="<?= esc($chat['name']) ?>">
                                       <?php else: ?>
                                           <div class="avatar-placeholder rounded-circle bg-primary text-white">
                                               <?= strtoupper(substr($chat['name'], 0, 1)) ?>
                                           </div>
                                       <?php endif; ?>
                                   </div>
                                   <div class="chat-item-content flex-grow-1">
                                       <h6 class="mb-1"><?= esc($chat['name']) ?></h6>
                                       <p class="mb-0 text-muted small">
                                           <?= word_limiter($chat['message'], 10) ?>
                                       </p>
                                   </div>
                                   <div class="chat-item-time">
                                       <small class="text-muted">
                                           <?= date('H:i', strtotime($chat['created_at'])) ?>
                                       </small>
                                   </div>
                               </div>
                           </div>
                       <?php endforeach; ?>
                   <?php endif; ?>
               </div>
           </div>
       </div>

       <!-- Chat Content -->
       <div class="col-md-8 col-lg-9 p-0">
           <div class="chat-content h-100 d-flex flex-column">
               <!-- Chat Header -->
               <div class="chat-header p-3 border-bottom">
                   <div class="d-flex align-items-center">
                       <div class="chat-avatar me-3">
                           <div class="avatar-placeholder rounded-circle bg-primary text-white">
                               <i class="fas fa-user"></i>
                           </div>
                       </div>
                       <div class="chat-user-info">
                           <h6 class="mb-0" id="currentChatUser">เลือกผู้สนทนา</h6>
                       </div>
                   </div>
               </div>

               <!-- Messages -->
               <div class="chat-messages flex-grow-1 p-3" id="messageContainer">
                   <div class="text-center text-muted py-5">
                       <i class="fas fa-comments fa-3x mb-3"></i>
                       <p>เลือกผู้สนทนาเพื่อเริ่มการแชท</p>
                   </div>
               </div>

               <!-- Message Input -->
               
               <div class="chat-input p-3 border-top">
                   <form id="messageForm" class="d-none">
                       <div class="input-group">
                           <input type="text" 
                               class="form-control" 
                               placeholder="พิมพ์ข้อความ..."
                               id="messageInput">
                               <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                           <button class="btn btn-primary" type="submit">
                               <i class="fas fa-paper-plane"></i>
                           </button>
                       </div>
                   </form>
               </div>
           </div>
       </div>
   </div>
</div>

<!-- Modal สำหรับเลือกผู้สนทนาใหม่ -->
<!-- Modal สำหรับเลือกประเภทการสนทนา -->
<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เริ่มการสนทนาใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- ตัวเลือกประเภทการสนทนา -->
                <div class="chat-type-selection mb-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card h-100 chat-type-card" data-type="user">
                                <div class="card-body text-center">
                                    <i class="fas fa-user fa-2x mb-2 text-primary"></i>
                                    <h6 class="card-title">สนทนากับผู้ใช้</h6>
                                    <p class="card-text small text-muted">พูดคุยกับผู้ใช้คนอื่น</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card h-100 chat-type-card" data-type="ai">
                                <div class="card-body text-center">
                                    <i class="fas fa-robot fa-2x mb-2 text-success"></i>
                                    <h6 class="card-title">สนทนากับ AI</h6>
                                    <p class="card-text small text-muted">พูดคุยกับผู้ช่วย AI</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่วนค้นหาผู้ใช้ (ซ่อนไว้ตอนแรก) -->
                <div id="userSearchSection" class="d-none">
                    <div class="input-group mb-3">
                        <input type="text" 
                               class="form-control" 
                               placeholder="ค้นหาผู้ใช้..." 
                               id="userSearchInput">
                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div id="userSearchResults" class="list-group">
                        <!-- ผลการค้นหาจะแสดงที่นี่ -->
                    </div>
                </div>

                <!-- ส่วนเลือกหัวข้อ AI (ซ่อนไว้ตอนแรก) -->
                <div id="aiTopicSection" class="d-none">
                    <div class="mb-3">
                        <label for="aiTopic" class="form-label">เลือกหัวข้อสนทนา</label>
                        <select class="form-select" id="aiTopic">
                            <option value="">-- เลือกหัวข้อ --</option>
                            <option value="general">ทั่วไป</option>
                            <option value="programming">การเขียนโปรแกรม</option>
                            <option value="math">คณิตศาสตร์</option>
                            <option value="science">วิทยาศาสตร์</option>
                            <option value="language">ภาษา</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100" id="startAiChat">
                        เริ่มสนทนากับ AI
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>




<style>
.chat-list {
   height: calc(100vh - 70px);
   overflow-y: auto;
}

.chat-item {
   cursor: pointer;
   transition: background-color 0.2s;
}

.chat-item:hover {
   background-color: #f8f9fa;
}

.chat-item.active {
   background-color: #e9ecef;
}

.avatar-placeholder {
   width: 50px;
   height: 50px;
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 20px;
}

.chat-messages {
   overflow-y: auto;
   background-color: #f8f9fa;
}

.message {
   max-width: 70%;
   margin-bottom: 1rem;
}

.message-content {
   padding: 0.75rem 1rem;
   border-radius: 1rem;
}

.message.sent {
   margin-left: auto;
}

.message.sent .message-content {
   background-color: #0d6efd;
   color: white;
}

.message.received .message-content {
   background-color: #e9ecef;
}

.chat-type-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.chat-type-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.chat-type-card.selected {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
}

.message.ai .message-content {
    background-color: #20c997;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
   .chat-list {
       height: 40vh;
   }
   
   .chat-content {
       height: 60vh !important;
   }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ประกาศตัวแปรที่ใช้งาน
    let currentRecipientId = null;
    const messageContainer = document.getElementById('messageContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const userSearchInput = document.getElementById('userSearchInput');
    const searchButton = document.getElementById('searchButton');
    const userSearchResults = document.getElementById('userSearchResults');
    const csrfToken = document.getElementById('<?= csrf_token() ?>').value;

    // ฟังก์ชันอัพเดต CSRF token
    function updateCsrfToken(newToken) {
        document.getElementById('<?= csrf_token() ?>').value = newToken;
        return newToken;
    }

    // ฟังก์ชันสร้าง headers สำหรับ fetch
    function getHeaders() {
        return {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.getElementById('<?= csrf_token() ?>').value,
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    // Event Listeners สำหรับ chat items
    document.querySelectorAll('.chat-item').forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.dataset.userId;
            loadChat(userId);
            
            // อัพเดท UI
            document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            messageForm.classList.remove('d-none');

            // อัพเดทชื่อผู้สนทนา
            const userName = this.querySelector('h6').textContent;
            document.getElementById('currentChatUser').textContent = userName;
        });
    });

    // ฟังก์ชันโหลดข้อความแชท
    function loadChat(userId) {
        currentRecipientId = userId;
        fetch(`/chat/getMessages/${userId}`, {
            headers: getHeaders()
        })
        .then(response => {
            const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
            if (newCsrfToken) updateCsrfToken(newCsrfToken);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayMessages(data.messages);
            }
        })
        .catch(error => {
            console.error('Error loading chat:', error);
            showAlert('error', 'ไม่สามารถโหลดข้อความได้');
        });
    }

    // ฟังก์ชันแสดงข้อความแชท
    function displayMessages(messages) {
        messageContainer.innerHTML = '';
        messages.reverse().forEach(message => {
            const isSent = message.sender_id == currentUserId; // currentUserId ต้องกำหนดค่าจาก PHP
            const messageTime = new Date(message.created_at).toLocaleTimeString('th-TH', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const messageHtml = `
                <div class="message ${isSent ? 'sent' : 'received'}">
                    <div class="message-content">
                        ${escapeHtml(message.message)}
                    </div>
                    <div class="message-time">
                        <small class="text-muted">
                            ${messageTime}
                        </small>
                    </div>
                </div>
            `;
            messageContainer.innerHTML += messageHtml;
        });
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    // ฟังก์ชันส่งข้อความ
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!messageInput.value.trim() || !currentRecipientId) return;
        
        const messageData = {
            recipient_id: currentRecipientId,
            message: messageInput.value.trim(),
            csrf_token: document.getElementById('<?= csrf_token() ?>').value
        };

        fetch('/chat/sendMessage', {
            method: 'POST',
           // headers: getHeaders(),
            body: JSON.stringify(messageData)
        })
        .then(response => {
            const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
            if (newCsrfToken) updateCsrfToken(newCsrfToken);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                loadChat(currentRecipientId);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            showAlert('error', 'ไม่สามารถส่งข้อความได้');
        });
    });

    // การค้นหาผู้ใช้
    let searchTimeout;
    userSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(searchUsers, 500);
    });

    // ฟังก์ชันค้นหาผู้ใช้
    function searchUsers() {
        const searchTerm = userSearchInput.value.trim();
        if (searchTerm.length < 2) {
            userSearchResults.innerHTML = '<p class="text-center text-muted my-3">พิมพ์อย่างน้อย 2 ตัวอักษรเพื่อค้นหา</p>';
            return;
        }

        fetch(`/chat/searchUsers?q=${encodeURIComponent(searchTerm)}`, {
            headers: getHeaders()
        })
        .then(response => {
            const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
            if (newCsrfToken) updateCsrfToken(newCsrfToken);
            return response.json();
        })
        .then(data => {
            if (data.users && data.users.length > 0) {
                displaySearchResults(data.users);
            } else {
                userSearchResults.innerHTML = '<p class="text-center text-muted my-3">ไม่พบผู้ใช้</p>';
            }
        })
        .catch(error => {
            console.error('Error searching users:', error);
            userSearchResults.innerHTML = '<p class="text-center text-danger my-3">เกิดข้อผิดพลาดในการค้นหา</p>';
        });
    }

    // ฟังก์ชันแสดงผลการค้นหา
    function displaySearchResults(users) {
        userSearchResults.innerHTML = users.map(user => `
            <a href="#" class="list-group-item list-group-item-action user-item" 
               data-user-id="${user.id}" 
               data-user-name="${escapeHtml(user.name)}"
               data-bs-dismiss="modal">
                <div class="d-flex align-items-center">
                    ${user.avatar 
                        ? `<img src="${user.avatar}" class="rounded-circle me-2" width="32" height="32" alt="${escapeHtml(user.name)}">` 
                        : `<div class="avatar-placeholder rounded-circle bg-primary text-white me-2" 
                               style="width:32px;height:32px;line-height:32px;text-align:center">
                               ${user.name.charAt(0).toUpperCase()}
                           </div>`
                    }
                    <div>
                        <h6 class="mb-0">${escapeHtml(user.name)}</h6>
                        ${user.email ? `<small class="text-muted">${escapeHtml(user.email)}</small>` : ''}
                    </div>
                </div>
            </a>
        `).join('');

        // เพิ่ม Event Listeners สำหรับผลการค้นหา
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.dataset.userId;
                const userName = this.dataset.userName;
                
                loadChat(userId);
                document.getElementById('currentChatUser').textContent = userName;
                messageForm.classList.remove('d-none');
                
                // ปิด Modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('newChatModal'));
                if (modal) modal.hide();
            });
        });
    }

    // ฟังก์ชัน Utility
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // ฟังก์ชันแสดง Alert
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.chat-content').prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Auto refresh ข้อความทุก 5 วินาที
    setInterval(() => {
        if (currentRecipientId) {
            loadChat(currentRecipientId);
        }
    }, 5000);
});







// เพิ่มในส่วน DOMContentLoaded
const chatTypeCards = document.querySelectorAll('.chat-type-card');
const userSearchSection = document.getElementById('userSearchSection');
const aiTopicSection = document.getElementById('aiTopicSection');
const startAiChat = document.getElementById('startAiChat');

// จัดการการเลือกประเภทการสนทนา
chatTypeCards.forEach(card => {
    card.addEventListener('click', function() {
        chatTypeCards.forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        
        const chatType = this.dataset.type;
        if (chatType === 'user') {
            userSearchSection.classList.remove('d-none');
            aiTopicSection.classList.add('d-none');
        } else {
            userSearchSection.classList.add('d-none');
            aiTopicSection.classList.remove('d-none');
        }
    });
});

// เริ่มการสนทนากับ AI
startAiChat.addEventListener('click', function() {
    const topic = document.getElementById('aiTopic').value;
    if (!topic) {
        showAlert('warning', 'กรุณาเลือกหัวข้อสนทนา');
        return;
    }

    // สร้าง AI chat session
    fetch('http://localhost:8080/chat/startAiChat', {
        method: 'POST',
       // headers: getHeaders(),
        body: JSON.stringify({
            topic: topic
         //   csrf_token: document.getElementById('<?= csrf_token() ?>').value
        })
    })
    .then(response => {
        const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
        if (newCsrfToken) updateCsrfToken(newCsrfToken);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            currentRecipientId = 'ai_' + data.sessionId;
            document.getElementById('currentChatUser').textContent = 'AI Assistant - ' + topic;
            messageForm.classList.remove('d-none');
            
            // ปิด Modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('newChatModal'));
            if (modal) modal.hide();

            // แสดงข้อความต้อนรับ
            const welcomeMessage = {
                sender_id: 'ai',
                message: data.welcomeMessage,
                created_at: new Date().toISOString()
            };
            displayMessages([welcomeMessage]);
        }
    })
    .catch(error => {
        console.error('Error starting AI chat:', error);
        showAlert('error', 'ไม่สามารถเริ่มการสนทนากับ AI ได้');
    });
});

// แก้ไขฟังก์ชัน sendMessage เพื่อรองรับการส่งข้อความหา AI
function sendMessage(message) {
    const endpoint = currentRecipientId.startsWith('ai_') 
        ? '/chat/sendAiMessage' 
        : '/chat/sendMessage';

    const messageData = {
        recipient_id: currentRecipientId,
        message: message,
        csrf_token: document.getElementById('<?= csrf_token() ?>').value
    };

    return fetch(endpoint, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(messageData)
    });
}


</script>