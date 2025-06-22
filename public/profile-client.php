<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header('Location: ../Program/auth.html');
    exit;
}
require_once __DIR__ . '/../app/models/ClientModel.php';
require_once __DIR__ . '/../app/models/OrderModel.php';

$client = ClientModel::getClientById($_SESSION['client_id']);
$orders = OrderModel::getOrdersByClient($_SESSION['client_id']);
$displayName = $client['name'] ?: $client['company_name'];
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
  <title>Личный кабинет клиента</title>
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
    <h1>Личный кабинет клиента <br> Добро пожаловать, <span id="client-name"><?php echo htmlspecialchars($displayName); ?></span></h1>
    <button id="notifications-btn" class="notify-btn"><img src="bell.svg" alt="Уведомления"></button>
  </div>
  <nav class="profile-menu">
    <button data-target="personal" class="profile-highlight active">Мой профиль</button>
    <button data-target="orders">Мои заказы</button>
  </nav>
  <div id="message"></div>
  <section class="profile-section" id="orders" style="display:none;">
    <button id="new-request-btn" class="btn" style="margin-bottom:10px;">Подать заявку</button>
    <div id="request-overlay" class="overlay">
        <form id="new-request-form" class="modal-form" style="margin-bottom:20px;" action="index.php?action=create_request" method="post">
          <label for="work-type">Тип работ:</label>
          <select id="work-type" name="work_type">
          <option>Экологический аудит</option>
          <option>Водный аудит</option>
          <option>Выбросы в атмосферу</option>
        </select>
        <button type="submit" class="btn">Отправить</button>
      </form>
    </div>
    <div class="table-container">
    <table class="orders-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Тип работ</th>
          <th>Статус</th>
          <th>Отчет</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($orders): ?>
        <?php foreach ($orders as $order): ?>
        <tr class="order-row">
          <td><?php echo htmlspecialchars($order['order_id']); ?></td>
          <td><?php echo htmlspecialchars($order['order_type']); ?></td>
          <td><?php echo htmlspecialchars($order['status']); ?></td>
          <td>
            <?php if (!empty($order['file_path'])): ?>
              <a href="<?php echo htmlspecialchars($order['file_path']); ?>" class="btn">Скачать</a>
            <?php else: ?>
              &mdash;
            <?php endif; ?>
          </td>
          <td>
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
    <?php if (!empty($client['name'])): ?>
      <p><strong>ФИО:</strong> <?php echo htmlspecialchars($client['name']); ?></p>
    <?php endif; ?>
    <?php if (!empty($client['company_name'])): ?>
      <p><strong>Организация:</strong> <?php echo htmlspecialchars($client['company_name']); ?></p>
    <?php endif; ?>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($client['email']); ?></p>
    <p><strong>Телефон:</strong> <?php echo htmlspecialchars($client['phone']); ?></p>
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

// переключение раздела (один раздел)
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

// показать форму новой заявки в затемненном окне
const newBtn = document.getElementById('new-request-btn');
const overlay = document.getElementById('request-overlay');
newBtn.addEventListener('click', () => {
  overlay.style.display = 'flex';
});

overlay.addEventListener('click', (e) => {
  if (e.target === overlay) overlay.style.display = 'none';
});

const notifBtn = document.getElementById('notifications-btn');
const notifOverlay = document.getElementById('notifications-overlay');
notifBtn.addEventListener('click', () => {
  notifOverlay.style.display = 'flex';
});
notifOverlay.addEventListener('click', (e) => {
  if (e.target === notifOverlay) notifOverlay.style.display = 'none';
});
// отображение сообщения об успешном запросе
const params = new URLSearchParams(window.location.search);
const msgBox = document.getElementById('message');
if (params.get('success')) {
  msgBox.textContent = 'Заявка успешно создана';
  msgBox.style.color = 'green';
} else if (params.get('error')) {
  msgBox.textContent = 'Ошибка при отправке заявки';
  msgBox.style.color = 'red';
}
// ограничение длины сообщений обрабатывается атрибутом maxlength
</script>
</body>
</html>
