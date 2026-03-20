# Nestify

A complete Nestify built with **PHP** & **MySQL** — designed as a BCA/B.Tech college project. Features role-based access for Admins and Students, room management, fee payments with detailed receipts, complaint tracking, and more.

Compatible with **XAMPP**, **WAMP**, and all servers running **PHP 5.4+** & **MySQL 5.x+**.

---

## Features

### Admin Panel
- Add, view, and edit student records
- Add, edit, and delete rooms (with Boy/Girl gender type)
- Allocate rooms to students
- Approve/Reject room requests from students
- Post notices and announcements
- View, accept, or reject student complaints (with category filters)
- Dashboard with live complaint statistics (Pending / Completed / Rejected)

### Student Portal
- Register and login to personal dashboard
- View assigned room details and leave room option
- Request available rooms or join a waitlist
- Pay hostel fee (Room Rent + Mess Fee + Maintenance Fee)
- View detailed fee receipts with full breakdown
- Submit complaints and track their status
- View hostel notices

---

## Tech Stack

| Technology | Version |
|---|---|
| PHP | 5.4+ (compatible up to 8.x) |
| MySQL | 5.x+ |
| HTML | 5 |
| CSS | 3 |
| Server | XAMPP / WAMP / Any Apache+PHP+MySQL |

---

## Project Structure

```
Hostel Management/
├── admin/
│   ├── add_notice.php
│   ├── add_room.php
│   ├── add_student.php
│   ├── allocate_room.php
│   ├── edit_room.php
│   ├── edit_student.php
│   ├── view_requests.php
│   └── view_students.php
├── css/
│   └── style.css
├── database/
│   └── database.sql
├── includes/
│   └── config.php
├── shared/
│   ├── view_complaint.php
│   ├── view_notices.php
│   └── view_rooms.php
├── student/
│   ├── add_complaint.php
│   ├── pay_fee.php
│   └── view_receipt.php
├── dashboard.php
├── index.php
├── login.php
├── logout.php
├── register.php
└── README.md
```

---

## How to Run

### Step 1: Setup Server
- Install **XAMPP** or **WAMP** on your system.
- Start **Apache** and **MySQL** from the control panel.

### Step 2: Copy Project
- Copy the entire `Hostel Management` folder into your server's root directory:
  - **XAMPP:** `C:\xampp\htdocs\`
  - **WAMP:** `C:\wamp\www\`

### Step 3: Create Database
1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Import the file `database/database.sql` — it will automatically create the `hostel_db` database and all required tables.

### Step 4: Run Gender Migration (One-Time)
- Open `http://localhost/Hostel%20Management/update_gender.php` in your browser once.
- This adds the `gender` column to the rooms table and updates student gender values.
- You can delete `update_gender.php` after running it.

### Step 5: Open the Application
- Visit: `http://localhost/Hostel%20Management/`

---

## Default Login Credentials

### Admin
| Field | Value |
|---|---|
| Email | `admin@hostel.com` |
| Password | `admin123` |

### Student
| Field | Value |
|---|---|
| Email | `test@test.com` |
| Password | `student123` |

> **Note:** When an admin adds a new student, the default password is the student's **Roll Number**.

---

## Database Tables

| Table | Purpose |
|---|---|
| `users` | Login credentials and roles (admin/student) |
| `students` | Student profiles, room assignments, fee details |
| `rooms` | Room info, capacity, available beds, gender type |
| `fees` | Payment history with fee breakdown |
| `complaints` | Student complaints with status tracking |
| `notices` | Hostel announcements and notices |
| `room_requests` | Room request/waitlist management |

---

## Fee Structure

Each student has a configurable fee breakdown:
- **Room Rent** (default: Rs 2000)
- **Mess Fee** (default: Rs 2500)
- **Maintenance Fee** (default: Rs 500)
- **Total**: Rs 5000 per year

Admins can customize these amounts per student via the Edit Student page.

---

## License

This project is for **educational purposes only**.
