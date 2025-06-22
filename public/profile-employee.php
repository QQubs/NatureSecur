<?php
session_start();
if (!isset($_SESSION['emp_id'])) {
    header('Location: ../Program/auth.html');
    exit;
}
require_once __DIR__ . '/../app/models/EmployeeModel.php';
require_once __DIR__ . '/../app/models/RequestModel.php';
require_once __DIR__ . '/../app/models/OrderModel.php';

$employee = EmployeeModel::getEmployeeById($_SESSION['emp_id']);
$requests = RequestModel::getAllRequests();
$orders = OrderModel::getOrdersByEmployee($_SESSION['emp_id']);
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
  <title>Личный кабинет сотрудника</title>
  <link rel="stylesheet" href="../Program/style.css">
</head>
<body>
<header>
  <a href="index.html" class="logo">
    <img src="LogoF3.png" alt="NatureSecur logo">
  </a>
  <nav>
    <ul>
      <li><a href="#services">Услуги</a></li>
      <li><a href="#about">О компании</a></li>
      <li><a href="#contacts">Контакты</a></li>
      <li><a href="auth.html" class="btn-auth">Выйти</a></li>
    </ul>
  </nav>
</header>
<main class="profile-container">
  <div class="profile-heading">
    <h1>Личный кабинет сотрудника <br> Добро пожаловать, <span id="employee-name"><?php echo htmlspecialchars($displayName); ?></span></h1>
    <button id="notifications-btn" class="notify-btn"><img src="bell.svg" alt="Уведомления"></button>
  </div>
  <nav class="profile-menu">
    <button data-target="personal" class="profile-highlight active">Мой профиль</button>
    <button data-target="requests">Заявки</button>
    <button data-target="orders">Заказы</button>
  </nav>
  <section class="profile-section" id="requests" style="display:none;">
    <h2>Заявки</h2>
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
            <button class="btn create-order-btn" data-id="<?php echo $req['request_id']; ?>">Создать заказ</button>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5">Заявок нет</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
    </div>
  </section>

  <section class="profile-section" id="orders" style="display:none;">
    <h2>Заказы</h2>
    <div class="table-container">
    <table class="orders-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Клиент</th>
          <th>Тип работ</th>
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
          <td>
            <select>
              <option<?php if ($order['status'] === 'принят') echo ' selected'; ?>>Принят</option>
              <option<?php if ($order['status'] === 'в работе') echo ' selected'; ?>>В работе</option>
              <option<?php if ($order['status'] === 'на проверке') echo ' selected'; ?>>На проверке</option>
              <option<?php if ($order['status'] === 'завершен') echo ' selected'; ?>>Завершен</option>
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
        <tr><td colspan="5">Заказов нет</td></tr>
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
      <h2>Уведомления</h2>
      <ul id="notifications-list">
        <li>Уведомлений нет</li>
      </ul>
    </div>
  </div>
</main>
<script>
// показать/скрыть чат
const toggles = document.querySelectorAll('.chat-toggle');
toggles.forEach(btn => {
  btn.addEventListener('click', () => {
    const chat = btn.parentElement.querySelector('.chat-area');
    chat.style.display = chat.style.display === 'block' ? 'none' : 'block';
  });
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
</script>
</body>
</html>
