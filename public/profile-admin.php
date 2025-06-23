<?php
session_start();
if (!isset($_SESSION['emp_id'])) {
    header('Location: ../Program/auth.html');
    exit;
}
require_once __DIR__ . '/../app/models/EmployeeModel.php';
$role = $_SESSION['role'] ?? '';
if ($role !== 'администратор') {
    header('Location: ../Program/auth.html');
    exit;
}
$employee = EmployeeModel::getEmployeeById($_SESSION['emp_id']);
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
  <title>Личный кабинет администратора</title>
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
    <h1>Личный кабинет администратора <br> Добро пожаловать, <span id="employee-name"><?php echo htmlspecialchars($displayName); ?></span></h1>
    <button id="notifications-btn" class="notify-btn"><img src="../Program/bell.svg" alt="Уведомления"></button>
  </div>
  <nav class="profile-menu">
    <button data-target="personal" class="profile-highlight active">Мой профиль</button>
    <button data-target="panel">Админ панель</button>
  </nav>
  <section class="profile-section" id="panel" style="display:none;">
    <nav class="profile-menu">
      <button data-target="users" class="profile-highlight active">Пользователи</button>
      <button data-target="admin-orders">Заказы</button>
      <button data-target="changes">Журнал изменений</button>
    </nav>
    <section class="profile-section" id="users" style="display:none;">
      <p>Управление пользователями: создание, редактирование, деактивация и удаление.</p>
    </section>
    <section class="profile-section" id="admin-orders" style="display:none;">
      <p>Управление заказами: переназначение ответственных, изменение сроков, редактирование и удаление заказов.</p>
    </section>
    <section class="profile-section" id="changes" style="display:none;">
      <p>Просмотр журнала изменений.</p>
    </section>
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
// переключение основного меню
const mainButtons = document.querySelectorAll('nav.profile-menu > button');
const mainSections = document.querySelectorAll('main > .profile-section');
mainButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    mainButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const target = btn.dataset.target;
    mainSections.forEach(sec => {
      sec.style.display = sec.id === target ? 'block' : 'none';
    });
  });
});
// показать первый раздел
document.querySelector('nav.profile-menu button.active').click();

// меню админ-панели
const adminButtons = document.querySelectorAll('#panel nav.profile-menu button');
const adminSections = document.querySelectorAll('#panel .profile-section');
adminButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    adminButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const target = btn.dataset.target;
    adminSections.forEach(sec => {
      sec.style.display = sec.id === target ? 'block' : 'none';
    });
  });
});
document.querySelector('#panel nav.profile-menu button.active').click();

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
