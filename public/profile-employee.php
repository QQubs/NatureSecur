<?php
session_start();
if (!isset($_SESSION['emp_id'])) {
    header('Location: ../Program/auth.html');
    exit;
}
require_once __DIR__ . '/../app/models/EmployeeModel.php';
require_once __DIR__ . '/../app/models/RequestModel.php';
require_once __DIR__ . '/../app/models/OrderModel.php';
require_once __DIR__ . '/../app/models/ClientModel.php';

$employee = EmployeeModel::getEmployeeById($_SESSION['emp_id']);
$requests = RequestModel::getAllRequests();
$orders = OrderModel::getOrdersByEmployee($_SESSION['emp_id']);
$clients = ClientModel::getAllClients();
$displayName = trim($employee['first_name'] . ' ' . $employee['second_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../Program/favicon.ico">
  <title>Личный кабинет сотрудника</title>
  <link rel="stylesheet" href="../Program/style.css">
</head>
<body>
<header>
  <a href="../Program/index.php" class="logo">
    <img src="../Program/LogoF3.png" alt="NatureSecur logo">
  </a>
  <nav>
    <ul>
      <li><a href="#services">Услуги</a></li>
      <li><a href="#about">О компании</a></li>
      <li><a href="#contacts">Контакты</a></li>
      <li><a href="logout.php" class="btn-auth">Выйти</a></li>
    </ul>
  </nav>
</header>
<main class="profile-container">
  <div class="profile-heading">
    <h1>Личный кабинет сотрудника <br> Добро пожаловать, <span id="employee-name"><?php echo htmlspecialchars($displayName); ?></span></h1>
    <button id="notifications-btn" class="notify-btn"><img src="../Program/bell.svg" alt="Уведомления"></button>
  </div>
  <nav class="profile-menu">
    <button data-target="personal" class="profile-highlight active">Мой профиль</button>
    <button data-target="requests">Заявки</button>
    <button data-target="orders">Заказы</button>
  </nav>
  <section class="profile-section" id="requests" style="display:none;">
    <h2>Заявки</h2>
    <div id="message"></div>
    <div class="table-container">
    <table class="requests-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Клиент</th>
          <th>Тип работ</th>
          <th>Дата</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($requests): ?>
        <?php foreach ($requests as $req): ?>
        <tr>
          <td><?php echo htmlspecialchars($req['request_id']); ?></td>
          <td><?php echo htmlspecialchars($req['client_name']); ?></td>
          <td><?php echo htmlspecialchars($req['order_type']); ?></td>
          <td><?php echo htmlspecialchars($req['request_date']); ?></td>
          <td>
            <button class="btn decline-btn" data-id="<?php echo $req['request_id']; ?>">Отклонить</button>
            <button class="btn create-order-btn" data-id="<?php echo $req['request_id']; ?>" data-client="<?php echo htmlspecialchars($req['client_name']); ?>" data-type="<?php echo htmlspecialchars($req['order_type']); ?>">Создать заказ</button>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5">Заявок нет</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
    </div>
    <div id="decline-overlay" class="overlay">
      <form id="decline-form" class="modal-form" action="index.php?action=decline_request" method="post">
        <input type="hidden" name="request_id" id="decline-request-id">
        <label for="decline-reason">Причина отклонения:</label>
        <textarea id="decline-reason" name="reason" required></textarea>
        <button type="submit" class="btn">Подтвердить</button>
      </form>
    </div>
    <div id="create-order-overlay" class="overlay">
      <form id="create-order-form" class="modal-form" action="index.php?action=create_order" method="post">
        <input type="hidden" name="request_id" id="create-request-id">
        <p><strong>Клиент:</strong> <span id="create-client-name"></span></p><br>
        <p><strong>Тип работ:</strong> <span id="create-order-type"></span></p><br>
        <p><strong>Сотрудник:</strong> <span id="create-employee-name"></span></p><br>
        <p><strong>Дата создания:</strong> <span id="create-date"></span></p><br>
        <label for="deadline">Дедлайн:</label>
        <input type="date" id="deadline" name="deadline" required>
        <button type="submit" class="btn">Создать заказ</button>
      </form>
    </div>
  </section>

<section class="profile-section" id="orders" style="display:none;">
    <h2>Заказы</h2>
    <button id="new-order-btn" class="btn" style="margin-bottom:10px;">Новый заказ</button>
    <div id="new-order-overlay" class="overlay">
      <form id="new-order-form" class="modal-form" action="index.php?action=new_order" method="post">
        <p><strong>Сотрудник:</strong> <?php echo htmlspecialchars($displayName); ?></p><br>
        <p><strong>Дата создания:</strong> <span id="new-order-date"></span></p><br>
         <label for="new-client">Клиент:</label>
        <select id="new-client" name="client_id" required>
          <option value="" disabled selected>Выберите клиента</option>
          <?php foreach ($clients as $cl): ?>
            <option value="<?php echo $cl['client_id']; ?>"><?php echo htmlspecialchars($cl['display_name']); ?></option>
          <?php endforeach; ?>
        </select><br><br>
        <label for="new-order-type">Тип работ:</label>
        <select id="new-order-type" name="order_type" required>
          <option>Экологический аудит</option>
          <option>Водный аудит</option>
          <option>Выбросы в атмосферу</option>
        </select><br><br>
        <label for="new-deadline">Дедлайн:</label>
        <input type="date" id="new-deadline" name="deadline" required><br><br>
        <button type="submit" class="btn">Создать заказ</button>
      </form>
    </div>
    <div class="table-container">
    <table class="orders-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Клиент</th>
          <th>Тип работ</th>
          <th>Дата</th>
          <th>Дедлайн</th>
          <th>Статус</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($orders): ?>
        <?php foreach ($orders as $order): ?>
        <tr class="order-row">
          <td><?php echo htmlspecialchars($order['order_id']); ?></td>
          <td><?php echo htmlspecialchars($order['client_name']); ?></td>
          <td><?php echo htmlspecialchars($order['order_type']); ?></td>
          <td><?php echo htmlspecialchars($order['order_date']); ?></td>
          <td><?php echo htmlspecialchars($order['deadline']); ?></td>
          <td>
            <select class="status-select" data-id="<?php echo $order['order_id']; ?>">
              <option value="принят"<?php if ($order['status'] === 'принят') echo ' selected'; ?>>Принят</option>
              <option value="в работе"<?php if ($order['status'] === 'в работе') echo ' selected'; ?>>В работе</option>
              <option value="на проверке"<?php if ($order['status'] === 'на проверке') echo ' selected'; ?>>На проверке</option>
              <option value="завершен"<?php if ($order['status'] === 'завершен') echo ' selected'; ?>>Завершен</option>
            </select>
          </td>
          <td>
            <label class="file-label">
              Прикрепить отчет
              <input type="file" accept="application/pdf">
            </label>
            <button class="btn chat-toggle">Чат</button>
            <div class="chat-area" style="display:none;">
              <div class="messages"></div>
              <textarea maxlength="500" placeholder="Введите сообщение"></textarea>
              <button class="btn send-message">Отправить</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7">Заказов нет</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
    </div>
  </section>
  <section class="profile-section" id="personal" style="display:none;">
    <h2>Личные данные</h2>
    <p><strong>ФИО:</strong> <?php echo htmlspecialchars($displayName); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($employee['email']); ?></p>
    <p><strong>Телефон:</strong> <?php echo htmlspecialchars($employee['phone']); ?></p>
  </section>
  <div id="notifications-overlay" class="overlay">
    <div class="modal-form">
      <h2>Уведомления</h2><br>
      <ul id="notifications-list">
        <li>Уведомлений нет</li>
      </ul>
    </div>
  </div>
  <div id="chat-overlay" class="overlay">
    <div id="chat-modal" class="modal-form"></div>
  </div>
</main>
<script>
// модальное окно чата
const toggles = document.querySelectorAll('.chat-toggle');
const chatOverlay = document.getElementById('chat-overlay');
const chatModal = document.getElementById('chat-modal');
let activeChatArea = null;
let activeChatParent = null;
toggles.forEach(btn => {
  btn.addEventListener('click', () => {
    activeChatArea = btn.nextElementSibling;
    activeChatParent = btn.parentNode;
    activeChatArea.style.display = 'block';
    chatModal.appendChild(activeChatArea);
    chatOverlay.style.display = 'flex';
  });
});

chatOverlay.addEventListener('click', (e) => {
  if (e.target === chatOverlay && activeChatArea) {
    activeChatArea.style.display = 'none';
    activeChatParent.appendChild(activeChatArea);
    chatOverlay.style.display = 'none';
  }
});
// ограничение размера файла
const fileInputs = document.querySelectorAll('input[type="file"]');
fileInputs.forEach(inp => {
  inp.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file && file.size > 20 * 1024 * 1024) {
      alert('Размер файла не должен превышать 20MB');
      e.target.value = '';
    }
  });
});

// переключение разделов по меню
const menuButtons = document.querySelectorAll('.profile-menu button');
const sections = document.querySelectorAll('.profile-section');
menuButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    menuButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const target = btn.dataset.target;
    sections.forEach(sec => {
      sec.style.display = sec.id === target ? 'block' : 'none';
    });
  });
});
// показать первый раздел по умолчанию
document.querySelector('.profile-menu button.active').click();

const notifBtn = document.getElementById('notifications-btn');
const notifOverlay = document.getElementById('notifications-overlay');
notifBtn.addEventListener('click', () => {
  notifOverlay.style.display = 'flex';
});
notifOverlay.addEventListener('click', (e) => {
  if (e.target === notifOverlay) notifOverlay.style.display = 'none';
});

// загрузка сообщений из localStorage
document.querySelectorAll('.order-row').forEach(row => {
  const id = row.querySelector('td').textContent.trim();
  row.dataset.orderId = id;
  row.querySelector('.chat-area').dataset.orderId = id;
  const key = 'chat-' + id;
  const messages = JSON.parse(localStorage.getItem(key) || '[]');
  const msgBox = row.querySelector('.messages');
  messages.forEach(m => {
    const p = document.createElement('p');
    p.innerHTML = '<strong>' + m.sender + ':</strong> ' + m.text;
    msgBox.appendChild(p);
  });
});

// отправка сообщения
document.querySelectorAll('.send-message').forEach(btn => {
  btn.addEventListener('click', () => {
    const area = btn.closest('.chat-area');
    const textArea = area.querySelector('textarea');
    const text = textArea.value.trim();
    if (!text) return;
    const id = area.dataset.orderId;
    const p = document.createElement('p');
    p.innerHTML = '<strong>Я:</strong> ' + text;
    area.querySelector('.messages').appendChild(p);
    const key = 'chat-' + id;
    const stored = JSON.parse(localStorage.getItem(key) || '[]');
    stored.push({sender:'Я', text});
    localStorage.setItem(key, JSON.stringify(stored));
    textArea.value = '';
  });
});

// обработка отклонения заявки
const declineButtons = document.querySelectorAll('.decline-btn');
const declineOverlay = document.getElementById('decline-overlay');
const declineInput = document.getElementById('decline-request-id');
declineButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    declineInput.value = btn.dataset.id;
    declineOverlay.style.display = 'flex';
  });
});
declineOverlay.addEventListener('click', (e) => {
  if (e.target === declineOverlay) declineOverlay.style.display = 'none';
});

// создание заказа из заявки
const createButtons = document.querySelectorAll('.create-order-btn');
const createOverlay = document.getElementById('create-order-overlay');
const createForm = document.getElementById('create-order-form');
const createInput = document.getElementById('create-request-id');
const createClient = document.getElementById('create-client-name');
const createType = document.getElementById('create-order-type');
const createEmployee = document.getElementById('create-employee-name');
const createDate = document.getElementById('create-date');
const employeeName = <?php echo json_encode($displayName); ?>;
createButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    createInput.value = btn.dataset.id;
    createClient.textContent = btn.dataset.client;
    createType.textContent = btn.dataset.type;
    createEmployee.textContent = employeeName;
    createDate.textContent = new Date().toISOString().slice(0, 10);
    createOverlay.style.display = 'flex';
  });
});
createOverlay.addEventListener('click', (e) => {
  if (e.target === createOverlay) createOverlay.style.display = 'none';
});

// создание нового заказа
const newOrderBtn = document.getElementById('new-order-btn');
const newOrderOverlay = document.getElementById('new-order-overlay');
const newOrderDate = document.getElementById('new-order-date');
newOrderBtn.addEventListener('click', () => {
  newOrderDate.textContent = new Date().toISOString().slice(0, 10);
  newOrderOverlay.style.display = 'flex';
});
newOrderOverlay.addEventListener('click', (e) => {
  if (e.target === newOrderOverlay) newOrderOverlay.style.display = 'none';
});

// изменение статуса заказа
document.querySelectorAll('.status-select').forEach(sel => {
  sel.addEventListener('change', () => {
    const orderId = sel.dataset.id;
    const status = sel.value;
    fetch('index.php?action=update_status', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({order_id: orderId, status: status})
    }).then(r => r.text()).then(t => {
      if (t.trim() !== 'ok') alert('Ошибка обновления статуса');
    });
  });
});

// ---------- фильтры по заказам ----------
function setupOrderFilters() {
  const table = document.querySelector('.orders-table');
  if (!table) return;
  const headerRow = table.querySelector('thead tr');
  const rows = Array.from(table.querySelectorAll('tbody tr'));
  const filterRow = document.createElement('tr');
  filterRow.className = 'filter-row';
  const filterCount = headerRow.children.length - 1;
  for (let i = 0; i < headerRow.children.length; i++) {
    const th = document.createElement('th');
    if (i >= filterCount) { filterRow.appendChild(th); continue; }
    const select = document.createElement('select');
    select.innerHTML = '<option value="">Все</option>';
    const values = new Set();
    rows.forEach(row => {
      const cell = row.children[i];
      let val = '';
      if (!cell) return;
      const sel = cell.querySelector('select');
      if (sel) {
        val = sel.value.trim();
      } else if (cell.querySelector('a')) {
        val = 'Есть';
      } else {
        val = cell.textContent.trim();
        if (val === '—') val = 'Нет';
      }
      values.add(val);
    });
    Array.from(values).sort().forEach(v => {
      const opt = document.createElement('option');
      opt.value = v;
      opt.textContent = v;
      select.appendChild(opt);
    });
    select.addEventListener('change', applyFilters);
    th.appendChild(select);
    filterRow.appendChild(th);
  }
  table.querySelector('thead').appendChild(filterRow);

  function applyFilters() {
    rows.forEach(row => {
      let show = true;
      filterRow.querySelectorAll('select').forEach((sel, idx) => {
        if (idx >= filterCount) return;
        const val = sel.value;
        if (!val) return;
        const td = row.children[idx];
        let cellVal = '';
        const s = td.querySelector('select');
        if (s) {
          cellVal = s.value.trim();
        } else if (td.querySelector('a')) {
          cellVal = 'Есть';
        } else {
          cellVal = td.textContent.trim();
          if (cellVal === '—') cellVal = 'Нет';
        }
        if (cellVal !== val) show = false;
      });
      row.style.display = show ? '' : 'none';
    });
  }
}
setupOrderFilters();

// сообщения об операциях
const params = new URLSearchParams(window.location.search);
const msgBox = document.getElementById('message');
if (params.get('success') === 'declined') {
  msgBox.textContent = 'Заявка отклонена';
  msgBox.style.color = 'green';
} else if (params.get('success') === 'created') {
  msgBox.textContent = 'Заказ успешно создан';
  msgBox.style.color = 'green';
} else if (params.get('error')) {
  msgBox.textContent = 'Ошибка выполнения операции';
  msgBox.style.color = 'red';
}
</script>
</body>
</html>
