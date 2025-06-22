-- Таблица: сотрудники
CREATE TABLE employees (
    emp_id SERIAL PRIMARY KEY,
    first_name TEXT NOT NULL,
    second_name TEXT NOT NULL,
    role TEXT CHECK (role IN ('сотрудник', 'администратор')) NOT NULL,
    email TEXT UNIQUE NOT NULL,
    phone TEXT,
    login TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);

-- Таблица: клиенты
CREATE TABLE clients (
    client_id SERIAL PRIMARY KEY,
    name TEXT,
    company_name TEXT,
    email TEXT UNIQUE NOT NULL,
    phone TEXT,
    password TEXT NOT NULL
);

-- Таблица: заявки
CREATE TABLE requests (
    request_id SERIAL PRIMARY KEY,
    client_id INT REFERENCES clients(client_id) ON DELETE CASCADE,
    request_date DATE NOT NULL,
    order_type TEXT NOT NULL
);

-- Таблица: заказы
CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    client_id INT REFERENCES clients(client_id) ON DELETE SET NULL,
    emp_id INT REFERENCES employees(emp_id) ON DELETE SET NULL,
    order_type TEXT NOT NULL,
    order_date DATE NOT NULL,
    deadline DATE,
    status TEXT CHECK (status IN ('принят', 'в работе', 'на проверке', 'завершен')) NOT NULL
);

-- Таблица: отчёты
CREATE TABLE reports (
    report_id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(order_id) ON DELETE CASCADE,
    emp_id INT REFERENCES employees(emp_id) ON DELETE SET NULL,
    upload_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    file_path TEXT NOT NULL,
    version INT NOT NULL
);

-- Таблица: журнал изменений
CREATE TABLE changelog (
    log_id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(order_id) ON DELETE CASCADE,
    emp_id INT REFERENCES employees(emp_id) ON DELETE SET NULL,
    change_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    action_type TEXT NOT NULL,
    description TEXT,
    old_value TEXT,
    new_value TEXT
);

-- Таблица: сообщения
CREATE TABLE messages (
    message_id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(order_id) ON DELETE CASCADE,
    client_id INT REFERENCES clients(client_id),
    emp_id INT REFERENCES employees(emp_id),
    author_type TEXT CHECK (author_type IN ('клиент', 'сотрудник')) NOT NULL,
    text TEXT CHECK (char_length(text) <= 500) NOT NULL,
    send_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);