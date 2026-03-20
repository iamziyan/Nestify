# ⬡ Nestify: Premium Hostel Management System

A state-of-the-art **Nestify** built with **PHP** & **MySQL**. This project has been modernized with a premium tech-themed design system, featuring glassmorphism, dark mode, and high-performance interactive components.

Designed as a BCA/B.Tech college project with production-grade aesthetics. Features role-based access for Admins and Students, comprehensive room logistics, automated fee auditing, and real-time complaint tracking.

---

## ✨ Premium UI Features

- **Glassmorphism**: Translucent UI elements with multi-layer blur effects.
- **Modern Tech Stack Design**: Deep charcoal and navy palettes with vibrant cyan and purple accents.
- **Animated Interface**: Shimmering headers, pulse status indicators, and smooth entrance transitions.
- **App Layout**: Structured sidebar-based navigation for a professional ERP feel.
- **Data Tables**: High-density information display with status-aware badges.

---

## 🛠️ Features

### Admin Panel
- **Dashboard**: Live statistics with visual status tracking for complaints and requests.
- **Student Center**: Add, view, and edit detailed student profiles and fee structures.
- **Room Engine**: Manage room types (Boy/Girl), capacity, and occupancy.
- **Allocation System**: Intelligent room assignment and request approval workflow.
- **Global Broadcast**: Post system-wide notices and announcements.
- **Complaint Management**: Filter and resolve student issues with a dedicated resolution suite.

### Student Portal
- **Smart Dashboard**: Instant view of assigned room status and personal metadata.
- **Room Booking**: Brows available rooms and submit allocation requests.
- **Digital Wallet**: Pay hostel fees (Rent, Mess, Maintenance) and download auto-generated **PDF-style receipts**.
- **Support Desk**: Lodge complaints and track resolution progress in real-time.
- **Notice Board**: Stay updated with the latest hostel announcements.

---

## 💻 Tech Stack

| Technology | Role | Version |
|---|---|---|
| **PHP** | Backend Logic | 5.4+ (Compatible with 8.x) |
| **MySQL** | Database Engine | 5.x+ |
| **CSS3** | Premium Design System | Custom Modern Theme |
| **HTML5** | Semantic Structure | Latest |
| **Server** | Local Environment | XAMPP / WAMP / Apache |

---

## 📂 Project Structure

```
Nestify/
├── admin/               # Administrative Management Suite
├── css/                 # Premium Design System (style.css)
├── database/            # Schema and SQL Imports
├── docs/                # Static UI Demo (HTML version)
├── includes/            # Core configuration & DB Connection
├── shared/              # Reusable Views (Notices, Rooms, Complaints)
├── student/             # Student Portal Pages
├── dashboard.php        # Unified Role-based Dashboard
├── index.php            # Entry Point & Redirector
├── login.php            │ Modernized Auth Flow
├── logout.php           │ Session Management
└── register.php         │ New Student Onboarding
```

---

## 🚀 How to Run

### 1. Setup Environment
- Install **XAMPP** or **WAMP**.
- Ensure **Apache** and **MySQL** services are active.

### 2. Deployment
- Clone/Copy the `Nestify` folder into your server root:
  - **XAMPP:** `htdocs/Nestify/`
  - **WAMP:** `www/Nestify/`

### 3. Database Initialization
1. Navigate to **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a database named `hostel_db`.
3. Import `database/database.sql` to generate tables and sample data.

### 4. Application Access
- Visit: `http://localhost/Nestify/`

---

## 🔑 Default Credentials

### Administrative Access
- **Email**: `admin@hostel.com`
- **Password**: `admin123`

### Standard Student Access
- **Email**: `test@test.com`
- **Password**: `student123`

> [!IMPORTANT]  
> New student accounts created by Admin use their **Roll Number** as the initial default password.

---

## 📑 Database Schema

| Table | High-Level Purpose |
|---|---|
| `users` | Secure authentication and RBAC |
| `students` | Profile data, financial state, and room links |
| `rooms` | Inventory management and gender-based grouping |
| `fees` | Transactional history and auditing |
| `complaints` | Issue tracking and resolution lifecycle |
| `notices` | System-wide broadcasting |
| `room_requests` | Allocation queue and waitlist logic |

---

## ⚖️ License

Developed by **@iamziyan**. Optimized for educational excellence and modern web standards.

