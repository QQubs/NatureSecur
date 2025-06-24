<?php
session_start();
if (!isset($_SESSION['emp_id'])) {
    header('Location: ../Program/auth.html');
    exit;
}
require_once __DIR__ . '/../app/models/EmployeeModel.php';
require_once __DIR__ . '/../app/models/ClientModel.php';
require_once __DIR__ . '/../app/models/OrderModel.php';
require_once __DIR__ . '/../app/models/ChangeLogModel.php';
$role = $_SESSION['role'] ?? '';
if ($role !== 'администратор') {
    header('Location: ../Program/auth.html');
    exit;
}
$employee = EmployeeModel::getEmployeeById($_SESSION['emp_id']);
$employees = EmployeeModel::getAllEmployees();
$clients = ClientModel::getAllClients();
$orders = OrderModel::getAllOrders();
$logs = ChangeLogModel::getAllLogs();
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
      <h2>Пользователи</h2>
      <div class="table-container">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Фамилия</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Логин</th>
            <th>Роль</th>
            <th>Пароль</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <form action="index.php?action=add_employee" method="post" style="display:contents;">
            <tr>
              <td>новый</td>
              <td><input name="first_name" required></td>
              <td><input name="second_name" required></td>
              <td><input name="email" required></td>
              <td><input name="phone"></td>
              <td><input name="login" required></td>
              <td>
                <select name="role">
                  <option value="сотрудник">Сотрудник</option>
                  <option value="администратор">Администратор</option>
                </select>
              </td>
              <td><input name="password" type="password" required></td>
              <td><button class="btn" type="submit">Добавить</button></td>
            </tr>
          </form>
          <?php foreach ($employees as $emp): ?>
          <form action="index.php?action=update_employee" method="post" style="display:contents;">
            <tr>
              <td><?php echo $emp['emp_id']; ?><input type="hidden" name="emp_id" value="<?php echo $emp['emp_id']; ?>"></td>
              <td><input name="first_name" value="<?php echo htmlspecialchars($emp['first_name']); ?>"></td>
              <td><input name="second_name" value="<?php echo htmlspecialchars($emp['second_name']); ?>"></td>
              <td><input name="email" value="<?php echo htmlspecialchars($emp['email']); ?>"></td>
              <td><input name="phone" value="<?php echo htmlspecialchars($emp['phone']); ?>"></td>
              <td><input name="login" value="<?php echo htmlspecialchars($emp['login']); ?>"></td>
              <td>
                <select name="role">
                  <option value="сотрудник"<?php if($emp['role']==='сотрудник') echo ' selected'; ?>>Сотрудник</option>
                  <option value="администратор"<?php if($emp['role']==='администратор') echo ' selected'; ?>>Администратор</option>
                </select>
              </td>
              <td></td>
              <td>
                <button class="btn" type="submit">Сохранить</button>
                <button class="btn" type="submit" name="delete" value="1" onclick="return confirm('Удалить пользователя?');">Удалить</button>
              </td>
            </tr>
          </form>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </section>
    <section class="profile-section" id="admin-orders" style="display:none;">
      <h2>Заказы</h2>
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
            <th>Сотрудник</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
          <form action="index.php?action=update_order" method="post" style="display:contents;">
            <tr>
              <td><?php echo $o['order_id']; ?><input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>"></td>
              <td>
                <select name="client_id">
                  <?php foreach ($clients as $cl): ?>
                    <option value="<?php echo $cl['client_id']; ?>"<?php if($cl['client_id']==$o['client_id']) echo ' selected'; ?>><?php echo htmlspecialchars($cl['display_name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><input name="order_type" value="<?php echo htmlspecialchars($o['order_type']); ?>"></td>
              <td><?php echo htmlspecialchars($o['order_date']); ?></td>
              <td><input type="date" name="deadline" value="<?php echo htmlspecialchars($o['deadline']); ?>"></td>
              <td>
                <select name="status">
                  <?php $statuses=['принят','в работе','на проверке','завершен']; foreach($statuses as $st): ?>
                    <option value="<?php echo $st; ?>"<?php if($o['status']===$st) echo ' selected'; ?>><?php echo $st; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select name="emp_id">
                  <option value="0">Нет</option>
                  <?php foreach ($employees as $e): ?>
                    <option value="<?php echo $e['emp_id']; ?>"<?php if($e['emp_id']==$o['emp_id']) echo ' selected'; ?>><?php echo htmlspecialchars($e['first_name'].' '.$e['second_name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <button class="btn" type="submit">Сохранить</button>
                <button class="btn" type="submit" name="delete" value="1" onclick="return confirm('Удалить заказ?');">Удалить</button>
              </td>
            </tr>
          </form>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </section>
    <section class="profile-section" id="changes" style="display:none;">
      <h2>Журнал изменений</h2>
      <div class="table-container">
      <table class="orders-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Заказ</th>
            <th>Сотрудник</th>
            <th>Дата</th>
            <th>Действие</th>
            <th>Описание</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($logs as $l): ?>
          <tr>
            <td><?php echo $l['log_id']; ?></td>
            <td><?php echo $l['order_id']; ?></td>
            <td><?php echo htmlspecialchars($l['employee_name']); ?></td>
            <td><?php echo htmlspecialchars($l['change_date']); ?></td>
            <td><?php echo htmlspecialchars($l['action_type']); ?></td>
            <td><?php echo htmlspecialchars($l['description']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
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
