# 🌙 NurCli  
### The CLI Companion for Tracking Salah & Qur’an Recitation

> Built by a Muslim, for Muslims.  
> Ramadan Kareem 🤍

---

## ✨ What is NurCli?

**NurCli** is a lightweight command-line tool that helps you:
- 🕌 Track your **Fard (obligatory) prayers**
- 📖 Log your **Qur’an recitation by pages**
- 🤲 Record **extra (nafl/sunnah) rakaas**
- 📊 View a clean **dashboard of your progress**

It’s simple, fast, and built for consistency because small daily actions build lasting discipline.

---

# 🚀 Features
## ✅ Track Fard Rakaas
Log the obligatory rakaas of any prayer.
```bash
nur fard <prayer>
```

Example:
```bash
nur fard fajr
```

---

## 📖 Log Qur’an Recitation
Track pages you’ve recited.
```bash
recite <page_from> <page_to>
```

Example:
```bash
recite 10 15
```

---

## 🤲 Record Extra Rakaas
Log voluntary prayers beyond fard.
```bash
extra <raka'a(s)>
```

Example:
```bash
extra 4
```

---

## 📊 View Your Status Dashboard
See your complete prayer & recitation summary.
```bash
status
```

---

## 📚 Get Detailed Help Per Command
To see a full help menu for any command, simply run it without arguments:
```bash
nur fard
recite
extra
```

Each command will display detailed usage instructions.

---

# 🛠 Installation Guide
Follow these steps carefully.

## 📌 Requirements
Before installing, make sure you have:
- ✅ **PHP CLI (Version 8.0+)**
- ✅ **MariaDB** or any SQL-based database  
- ⚠️ SQLite is **NOT supported**

---

# 🔧 Step-by-Step Setup

## 1️⃣ Clone the Repository

```bash
git clone https://github.com/LethalRam/NurCli/
cd NurCli
```

---

## 2️⃣ Create the Database

- Locate the file:

```
schema.sql
```

- Import it into your SQL server to create the database structure.

Example:

```bash
mysql -u username -p database_name < schema.sql
```

---

## 3️⃣ Configure Database Connection

Open:

```
NurCli/db/env.php
```

Edit the database credentials:

```php
define('DB_HOST', 'HOST HERE');
define('DB_USER', 'USERNAME HERE');
define('DB_PASS', 'PASSWORD HERE');
define('DB_NAME', 'NurDB');
```

Make sure all values are correct.

---

## 4️⃣ Configure the Bash Wrapper

- In the root of the repository, locate the bash wrapper file.
- Edit the file and set the correct path to your `nur.php`.

Example:

```bash
php /full/path/to/nur.php "$@"
```

---

## 5️⃣ Make It Globally Accessible

Move the wrapper to:

```bash
/usr/local/bin/
```

Example:

```bash
sudo mv nur /usr/local/bin/
sudo chmod +x /usr/local/bin/nur
```

---

## 6️⃣ Test Installation

From anywhere in your terminal, run:

```bash
nur
```

If the help menu appears 🎉 you installed it successfully!

---

# 🧯 Troubleshooting

If you're having database connection issues, check the following:

## 🔎 Database Type

- Make sure you are using **MariaDB or MySQL**
- ❌ Do NOT use SQLite

---

## 🔐 User Privileges

Ensure your database user:

- Has proper privileges
- Has correct host access:
  - `'username'@'127.0.0.1'`
  - `'username'@'localhost'`
  - Or `'username'@'%'` (⚠️ use `%` carefully)

---

## 🧾 Double Check Credentials

Verify:

- DB host
- DB username
- DB password
- DB name

All must match your actual database configuration.

---

## 📂 Wrapper Path

Make sure the bash wrapper contains the correct path to your `nur.php`.

---

# 🤍 Why NurCli?

Consistency in worship is easier when tracked.

NurCli helps you:

- Build discipline
- Stay accountable
- Visualize your worship progress
- Stay motivated especially during Ramadan

---

# 📌 Final Notes

This project is open-source and built with sincerity.

If it benefits you:

- ⭐ Star the repository
- 🤝 Contribute
- 🛠 Improve it
- 📢 Share it

May Allah put barakah in your time and actions.

**Ramadan Kareem 🌙**